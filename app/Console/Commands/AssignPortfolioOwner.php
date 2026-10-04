<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

#[Signature('portfolio:assign-owner {portfolio} {email}')]
#[Description('Assign an existing unowned portfolio to its registered owner.')]
class AssignPortfolioOwner extends Command
{
    public function handle(): int
    {
        $user = User::query()->where('email', Str::lower(trim((string) $this->argument('email'))))->first();
        if ($user === null) {
            $this->error('No account exists for that email. Ask the owner to register first.');

            return self::FAILURE;
        }

        $portfolio = DB::table('portfolios')->where('id', $this->argument('portfolio'))->first();
        if ($portfolio === null) {
            $this->error('Portfolio not found.');

            return self::FAILURE;
        }

        if ($portfolio->user_id !== null) {
            $this->error('This portfolio already has an owner. Ownership was not changed.');

            return self::FAILURE;
        }

        DB::table('portfolios')
            ->where('id', $portfolio->id)
            ->whereNull('user_id')
            ->update(['user_id' => $user->getKey()]);

        $this->info('Portfolio ownership assigned.');

        return self::SUCCESS;
    }
}
