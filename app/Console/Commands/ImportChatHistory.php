<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Team;
use App\Models\ChatMessage;

class ImportChatHistory extends Command
{
    protected $signature = 'chat:import {teamId} {filePath}';
    protected $description = 'Import chat history from a text file for a specific team';

    public function handle(): void
    {
        $teamId = $this->argument('teamId');
        $filePath = storage_path('app/' . $this->argument('filePath'));

        if (!file_exists($filePath)) {
            $this->error("File does not exist: $filePath");
            return;
        }

        $team = Team::with('users')->find($teamId);
        if (! $team) {
            $this->error("Team not found with ID $teamId");
            return;
	}

	$team->load('users');

	// Debug:
        $this->info('Users in team: ' . $team->users->pluck('name')->join(', '));

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $imported = 0;

        foreach ($lines as $line) {
            if (!str_contains($line, ':')) continue;

            [$name, $message] = explode(':', $line, 2);
            $name = trim($name);
            $message = trim($message);

            $user = User::where('name', $name)->first();

            if (!$user || !$team->users->contains($user)) {
                $this->warn("Skipping message by unknown or non-team user: $name");
                continue;
            }

            ChatMessage::create([
                'team_id' => $team->id,
                'user_id' => $user->id,
                'message' => $message,
            ]);

            $imported++;
        }

        $this->info("Imported $imported messages into team ID $teamId.");
    }
}
