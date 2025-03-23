<?php

namespace App\Livewire;

use App\Models\Task;
use App\Models\Subtask;
use App\Models\Comment;
use App\Models\TaskComment;
use App\Models\TaskAttachment;
use App\Traits\WithNotifications;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TaskCard extends Component
{
    use WithFileUploads;
    use WithNotifications;

    public Task $task;
    public $showDetails = false;
    public $newSubtaskTitle = '';
    public $newCommentContent = '';
    public $attachments = [];
    public $editMode = false;
    public $editedTask;

    protected $listeners = ['taskUpdated' => '$refresh'];

    public function mount(Task $task)
    {
        $this->task = $task;
        $this->editedTask = [
            'title' => $task->title,
            'description' => $task->description,
            'due_date' => $task->due_date ? $task->due_date->format('Y-m-d') : null
        ];
    }

    public function toggleDetails()
    {
        $this->showDetails = !$this->showDetails;
    }

    public function toggleEditMode()
    {
        $this->editMode = !$this->editMode;
        if (!$this->editMode) {
            $this->editedTask = [
                'title' => $this->task->title,
                'description' => $this->task->description,
                'due_date' => $this->task->due_date ? $this->task->due_date->format('Y-m-d') : null
            ];
        }
    }

    public function updateTask()
    {
        $validated = $this->validate([
            'editedTask.title' => 'required|string|max:255',
            'editedTask.description' => 'nullable|string',
            'editedTask.due_date' => 'nullable|date'
        ]);

        $this->task->update([
            'title' => $validated['editedTask']['title'],
            'description' => $validated['editedTask']['description'],
            'due_date' => $validated['editedTask']['due_date']
        ]);

        $this->editMode = false;
        $this->notifySuccess('Tarefa atualizada com sucesso!');
    }

    public function addSubtask()
    {
        $this->validate([
            'newSubtaskTitle' => 'required|string|max:255'
        ]);

        $this->task->subtasks()->create([
            'title' => $this->newSubtaskTitle,
            'is_completed' => false
        ]);

        $this->newSubtaskTitle = '';
        $this->notifySuccess('Subtarefa adicionada com sucesso!');
    }

    public function toggleSubtask($subtaskId)
    {
        $subtask = $this->task->subtasks()->findOrFail($subtaskId);
        $subtask->update(['is_completed' => !$subtask->is_completed]);
        $this->notifySuccess('Status da subtarefa atualizado!');
    }

    public function deleteSubtask($subtaskId)
    {
        $this->task->subtasks()->findOrFail($subtaskId)->delete();
        $this->notifySuccess('Subtarefa removida com sucesso!');
    }

    public function addComment()
    {
        $this->validate([
            'newCommentContent' => 'required|string'
        ]);

        $this->task->comments()->create([
            'content' => $this->newCommentContent,
            'user_id' => auth()->id()
        ]);

        $this->newCommentContent = '';
        $this->notifySuccess('Comentário adicionado com sucesso!');
    }

    public function uploadAttachments()
    {
        $this->validate([
            'attachments.*' => 'required|file|max:10240'
        ]);

        foreach ($this->attachments as $attachment) {
            $filename = $attachment->getClientOriginalName();
            $path = $attachment->store('task-attachments', 'public');

            $this->task->attachments()->create([
                'filename' => $filename,
                'file_path' => $path,
                'uploader_id' => auth()->id()
            ]);
        }

        $this->attachments = [];
        $this->notifySuccess('Arquivos anexados com sucesso!');
    }

    public function deleteAttachment($attachmentId)
    {
        $attachment = $this->task->attachments()->findOrFail($attachmentId);
        Storage::disk('public')->delete($attachment->file_path);
        $attachment->delete();
        $this->notifySuccess('Anexo removido com sucesso!');
    }

    public function updateStatus($status)
    {
        $this->task->update(['status' => $status]);
        $this->notifySuccess('Status da tarefa atualizado com sucesso!');
    }

    public function render()
    {
        return view('livewire.task-card', [
            'subtasks' => $this->task->subtasks()->orderBy('created_at', 'desc')->get(),
            'comments' => $this->task->comments()->with('user')->orderBy('created_at', 'desc')->get(),
            'attachments' => $this->task->attachments()->with('uploader')->orderBy('created_at', 'desc')->get(),
            'dependencies' => $this->task->dependencies,
            'completedSubtasksCount' => $this->task->subtasks()->where('is_completed', true)->count(),
            'totalSubtasksCount' => $this->task->subtasks()->count()
        ]);
    }
} 