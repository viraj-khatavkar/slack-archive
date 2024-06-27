<?php

namespace App\Console\Commands;

use App\Models\Channel;
use App\Models\Message;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImportSlackArchivesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'slack:import-archives {--users} {--messages} {--map-threads}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $importUsers = $this->option('users');
        $importMessages = $this->option('messages');
        $mapThreads = $this->option('map-threads');

        if ($importUsers) {
            $this->info('Importing Users...');
            $this->importUsers();
        }

        if ($importMessages) {
            $this->info('Importing Messages...');

            $channels = Channel::all();

            foreach ($channels as $channel) {
                $this->newLine();
                $this->info('Importing Messages for ' . $channel->name);
                $listOfFiles = Storage::files('slack-archive/' . $channel->name);

                $bar = $this->output->createProgressBar(count($listOfFiles));
                $bar->start();

                foreach ($listOfFiles as $file) {
                    if (Str::endsWith($file, '.json')) {
                        $this->importMessages($file, $channel);
                    }
                    $bar->advance();
                }

                $bar->finish();
            }
        }

        if ($mapThreads) {
            $this->info('Mapping Threads...');
            $this->mapThreads();
        }
    }

    protected function importMessages($fileName, Channel $channel)
    {
        $contents = File::get(Storage::path($fileName));
        $messages = json_decode($contents, associative: true);

        foreach ($messages as $message) {
            if (!isset($message['type'])){
                continue;
            }

            if ($message['type'] !== 'message') {
                continue;
            }

            if (isset($message['subtype']) && in_array($message['subtype'], ['bot_message', 'channel_join', 'tombstone'])) {
                continue;
            }

            if (!isset($message['user'])) {
                $this->info('user not found ' . $fileName . ' ' . $message['ts']);
                continue;
            }

            $user = User::where('slack_user_id', $message['user'])->first();

            if (!$user) {
                $this->info('user not found ' . $fileName . ' ' . $message['ts']);
                continue;
            }

            if(stripos($message['text'],'<http')!==false) {
                $linksInMessage = explode('<http', $message['text']);
                foreach ($linksInMessage as $linkInMessage) {
                    $array = explode('>', $linkInMessage);
                    $linkTotalInBrackets = $array[0];
                    $array = explode('|', $array[0]);
                    $linkInMessage = $array[0];
                    $message['text'] = str_replace(
                        '<http' . $linkTotalInBrackets . '>',
                        '<a href="http' . $linkInMessage . '" target="_blank">http' . $linkInMessage . '</a>',
                        $message['text']
                    );
                }
            }

            while (Str::of($message['text'])->contains('<@')) {
                $str = Str::of($message['text'])
                    ->betweenFirst('<@', '>');

                $atUser = User::where('slack_user_id', $str)->first();

                $message['text'] = Str::of($message['text'])
                    ->replaceFirst('<@' . $str . '>', '<strong>@' . $atUser->name . '</strong>')
                    ->value();
            }

            $slackMessageTime = Carbon::createFromTimestamp($message['ts'])->shiftTimezone('UTC')
                ->setTimezone('Asia/Kolkata')
                ->format('Y-m-d H:i:s');

            Message::create([
                'channel_id' => $channel->id,
                'user_id' => $user->id,
                'content' => $message['text'],
                'ts' => $message['ts'],
                'thread_ts' => $message['thread_ts'] ?? null,
                'slack_timestamp' => $slackMessageTime,
            ]);
        }
    }

    protected function mapThreads()
    {
        $messages = Message::whereNotNull('thread_ts')->whereRaw('thread_ts = ts')->get();
        $bar = $this->output->createProgressBar(count($messages));

        foreach ($messages as $message) {
            $bar->advance();
            DB::table('messages')
                ->where('thread_ts', $message->ts)
                ->where('channel_id', $message->channel_id)
                ->whereRaw('thread_ts != ts')
                ->update([
                    'parent_id' => $message->id
                ]);
        }
        $bar->finish();
    }

    protected function importUsers()
    {
        DB::table('users')->truncate();
        $contents = File::get(Storage::path('slack-archive/users.json'));
        $users = json_decode($contents, associative: true);

        $bar = $this->output->createProgressBar(count($users));
        $bar->start();

        foreach ($users as $user) {
            User::create([
                'name' => empty($user['profile']['display_name']) ? $user['profile']['real_name'] : $user['profile']['display_name'],
                'image_url' => $user['profile']['image_72'],
                'slack_user_id' => $user['id'],
            ]);
            $bar->advance();
        }

        $bar->finish();
    }
}
