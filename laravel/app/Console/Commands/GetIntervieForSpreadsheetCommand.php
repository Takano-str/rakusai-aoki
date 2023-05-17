<?php

namespace App\Console\Commands;

use App\Models\Consumer;
use App\Models\InterviewInfoForSpreadsheet;
use App\Services\Google\SpreadService;
use Illuminate\Console\Command;

class GetIntervieForSpreadsheetCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:getInterviewForSpread';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '面接確定情報をスプレッドシートに転記する';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $targetConsumerList = InterviewInfoForSpreadsheet::with(['consumer' => function ($query) {
            $query->with('store');
            $query->with('consumerDetail');
            $query->with('worksheet');
        }])
            ->where('write_status', 'not_yet')
            ->get()
            ->toArray();

        if (empty($targetConsumerList)) {
            \Log::info('スプレッドシートへの転記対象が見つかりませんでした。');
            return Command::SUCCESS;
        }

        $consumerIdList = [];
        $insertValues = [];

        \Log::debug($targetConsumerList);
        foreach ($targetConsumerList as $targetConsumer) {
            $row = [];
            $createDate = $targetConsumer['consumer']['created_at'];
            $createDate = str_replace('T', ' ', $createDate);
            $createDate = str_replace('.000000Z', '', $createDate);
            $decideDate = $targetConsumer['decide_date'];

            // 求職者情報
            $consumer = $targetConsumer['consumer'];
            $consumerId = $consumer['id'];
            $atsId = $consumer['ats_id'] ?? '';
            $consumerName = $consumer['consumer_detail']['name'] ?? '';
            $storeName = $consumer['store']['store_name'] ?? '';
            $storeName = $consumer['store']['store_name'] ?? '';

            // アンケート回答結果
            $worksheetAnswer = $consumer['worksheet']['worksheet_answer'] ?? '';
            $worksheetAnswerList = [];
            $birthday = '';
            $jobtype = '';
            $interviewPlace = '';
            $motivation = '';
            // \Log::debug($worksheetAnswer);
            // \Log::debug($worksheetAnswerList);
            if (!empty($worksheetAnswer)) {
                $worksheetAnswerList = json_decode($worksheetAnswer, true);
                $birthday = $worksheetAnswerList['birthYear'] . '-';
                $birthday .= $worksheetAnswerList['birthMonth'] . '-';
                $birthday .= $worksheetAnswerList['birthDay'] ?? '';
                \Log::debug($birthday);
                $jobtype = $worksheetAnswerList['jobtype'] ?? '';
                $interviewPlace = $worksheetAnswerList['interviewPlace'] ?? '';
                $motivation = $worksheetAnswerList['motivation'] ?? '';
            }

            $row[] = $createDate;
            $row[] = $decideDate;
            $row[] = $atsId;
            $row[] = $storeName;
            $row[] = $consumerName;
            $row[] = $birthday;
            $row[] = $jobtype;
            $row[] = $interviewPlace;
            $row[] = $motivation;
            $row[] = 'https://ats.jobop.jp/ats/application/applicationDetail/?applicationId=' . $atsId;

            $insertValues[] = $row;
            $consumerIdList[] = $consumerId;
        }

        $spreadId = env('APPLICANT_INTERVIEW_SPREADSHEET_ID');
        $range = '面接確定_応募者リスト!A1';
        $spreadService = new SpreadService($spreadId, $range);
        $spreadService->updateSpreadRow($range, $insertValues);

        InterviewInfoForSpreadsheet::whereIn('consumer_id', $consumerIdList)
            ->update([
                'write_status' => 'complete'
            ]);

        \Log::info('スプレッドシートへの転記処理が完了しました。');
        return Command::SUCCESS;
    }
}
