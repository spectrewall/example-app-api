<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DeleteTrashedUsersOrCompanies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deleteTrashedUsersOrCompanies';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete trashed users or companies';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $oneMonthAgo = Carbon::now()->subDays(30)->toDate();
        User::onlyTrashed()->whereDate('deleted_at', '<=', $oneMonthAgo)->chunk(50, function ($users) {
            foreach ($users as $user) {
                $user->address()->delete();
                \Storage::disk('public')->delete($user->profile_picture);
            }
        });
        Company::onlyTrashed()->whereDate('deleted_at', '<=', $oneMonthAgo)->chunk(50, function ($companies) {
            foreach ($companies as $company) {
                $company->address()->delete();
            }
        });
        return 0;
    }
}
