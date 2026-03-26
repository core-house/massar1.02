<?php

declare(strict_types=1);

namespace Modules\Agent\Policies;

use App\Models\User;
use Modules\Agent\Models\AgentQuestion;

class AgentQuestionPolicy
{
    /**
     * Determine whether the user can view any questions.
     */
    public function viewAny(User $user): bool
    {
        // Check if user has permission to access agent module
        if (method_exists($user, 'hasPermissionTo')) {
            return $user->hasPermissionTo('agent.access');
        }

        // Fallback: allow all authenticated users
        return true;
    }

    /**
     * Determine whether the user can create questions.
     */
    public function create(User $user): bool
    {
        // Check if user has permission to ask questions
        if (method_exists($user, 'hasPermissionTo')) {
            return $user->hasPermissionTo('agent.ask');
        }

        // Fallback: allow all authenticated users
        return true;
    }

    /**
     * Determine whether the user can view the question.
     * Users can only view their own questions.
     */
    public function view(User $user, AgentQuestion $question): bool
    {
        return $question->user_id === $user->id;
    }

    /**
     * Determine whether the user can update the question.
     * Questions cannot be updated after creation.
     */
    public function update(User $user, AgentQuestion $question): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the question.
     * Questions cannot be deleted by users.
     */
    public function delete(User $user, AgentQuestion $question): bool
    {
        return false;
    }
}
