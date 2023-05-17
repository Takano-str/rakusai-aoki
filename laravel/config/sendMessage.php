<?php
/**
 * ステータス毎の送信するメッセージ
 */
/** 最初 */
// $new = "【選考のご案内】\n";
$new = "";
$new .= "NXキャリアロードです。面接希望日をご回答下さい\n";
$new .= "WEB面接は土日祝も実施しております。\n";
$new .= "{{url}}\n";
// $new .= "下記URLよりweb選考にお進みください。\n";
// $new .= "こちらのURLは3日以内にご入力ください。\n";
// $new .= "{{url}}\n";

$new_mail = "";
$new_mail .= "{{name}}様\n";
$new_mail .= "\n";
$new_mail .= "この度は弊社求人にご応募いただき、ありがとうございます。\n";
$new_mail .= "NXキャリアロード㈱採用担当でございます。\n";
$new_mail .= "\n";
$new_mail .= "面接に際して事前アンケートのご回答をお願い致します。\n";
$new_mail .= "詳細は下記URLよりご確認ください。\n";
$new_mail .= "\n";
$new_mail .= "{{url}}\n";
$new_mail .= "\n";
$new_mail .= "◇ご注意◇\n";
$new_mail .= "本メールはシステムより自動配信されています。\n";
$new_mail .= "返信は受付できませんので、ご了承ください。\n";


/** 年末対応 **/ 
// $new .= "※12/30(金)～1/3(火)までは年末年始休業とさせていただきます。\n";
// $new .= "※お問い合わせについては4日以降順次回答させていただきます。\n";

/** ゴールデンウイーク対応 **/ 
// $new .= "※4/29(金)～5/8(日)まではGW休業とさせていただきます。\n";
$new .= "※このSMSは送信専用です。";

/** 続けて */
$second = "尚、こちらのURLは5日期限となっております。\n";
$second .= "期限切れとなった場合はお手数ですが下記までご連絡ください。\n";
$second .= "株式会社 XXXXX 050-YYYY-ZZZZ（平日9:00-19:00）";

/** 重複応募 */
$infoDuplicate = "ご応募ありがとうございます。";
$infoDuplicate .= "\n送信済みのアンケートから回答お願いします。";

/** 求人終了 */
$applicationEnd = "【選考について】\n";
$applicationEnd .= "大変申し訳ございません。\n";
$applicationEnd .= "ご応募いただいた求人は募集を終了いたしました。\n";
$applicationEnd .= "別の職種・別の店舗へ再度ご応募をお願いいたします。";

/** 合格 */
// $pass = "【選考結果のご連絡】\n";
// $pass .= "Web選考に合格しました。\n";
// $pass .= "おめでとうございます！\n";
// $pass .= "○面接案内↓\n";
// $pass .= "{{url}}\n";
$pass = "NXキャリアロード㈱です。\n";
$pass .= "面接日が確定しました。詳細をメールアドレスに送信しましたのでご確認下さい。届いていない場合はご連絡ください。\n";
$pass .= "【お問い合わせ先】\n";
$pass .= "ＮＸキャリアロード㈱EC営業部\n";
$pass .= "TEL：050-3819-7869\n";
$pass .= "MAIL:shutokenec@careerroad.co.jp\n";

$pass_mail = "";
$pass_mail .= "{{name}}様\n";
$pass_mail .= "\n";
$pass_mail .= "NXキャリアロード株式会社\n";
$pass_mail .= "採用担当です。\n";
$pass_mail .= "下記日程で面接を設定しました。\n";
$pass_mail .= "面接日時：{{interview_date}}\n";
$pass_mail .= "面接方式：{{interview_type}}\n";
$pass_mail .= "{{interview_venue_address}}\n";

$pass_mail .= "\n";
$pass_mail .= "面接案内URL：\n";
$pass_mail .= "{{url}}\n";


// $pass_mail .= "[branch_address]\n";
// $pass_mail .= "URL：\n";
// $pass_mail .= "{{url}}\n";
$pass_mail .= "\n";
$pass_mail .= "日程変更、キャンセルは\n";
$pass_mail .= "下記からお伝えください。\n";
// $pass_mail .= "[chatbot_url]\n";
$pass_mail .= "\n";
$pass_mail .= "【ご用意頂くもの】\n";
$pass_mail .= "1.現住所記載の身分証明書\n";
$pass_mail .= "2.ボールペン\n";
$pass_mail .= "\n";

$pass_mail .= "【持物】\n";
$pass_mail .= "・現住所の確認できる身分証明書\n";
$pass_mail .= "・ボールペン\n";
$pass_mail .= "※学生の方→学生証\n";
$pass_mail .= "※外国籍の方→在留カード、パスポート\n";
$pass_mail .= "\n";
$pass_mail .= "【服装】\n";
$pass_mail .= "私服で結構です。\n";
$pass_mail .= "\n";
$pass_mail .= "【事前入力】\n";
$pass_mail .= "◇下記URLからアクセスして\n";
$pass_mail .= "　面接日までに各項目のご入力をお願い致します。\n";
$pass_mail .= "URL：\n";
$pass_mail .= "https://forms.gle/fxuEq65NURxuJmiv9\n";
$pass_mail .= "\n";

$pass_mail .= "{{name}}様 のご来社を心待ちにしております。\n";
$pass_mail .= "\n";
$pass_mail .= "【注意】\n";
$pass_mail .= "本メールは自動配信の為\n";
$pass_mail .= "お問合せは下記連絡先までお願いします。\n";
$pass_mail .= "\n";
$pass_mail .= "【お問い合わせ先】\n";
$pass_mail .= "ＮＸキャリアロード㈱EC営業部\n";
$pass_mail .= "TEL：050-3819-7869\n";
$pass_mail .= "MAIL:shutokenec@careerroad.co.jp\n";



/** 前日リマインド */
$theDayBefore = "下記のリンクから面接内容をご確認ください。\n";
$theDayBefore .= "{{url}}";

/** 【修正版】 2日前リマインド **/
$the_day_two_days_before = "面接は{{interview_date}}～となっております。\n";
$the_day_two_days_before .= "ご確認お願いいたします。\n";
$the_day_two_days_before .= "{{url}}\n\n";
$the_day_two_days_before .= "キャンセル済みでこの通知が届いた場合は行き違いとなります。ご容赦ください。";

/** 【修正版】 前日リマインド 1 **/
// $the_day_before_1 = "明日の面接は{{interview_date}}～となります。\n";
// $the_day_before_1 .= "お会いできることを楽しみにしています！\n";
// $the_day_before_1 .= "{{url}}\n\n";
// $the_day_before_1 .= "キャンセル済みでこの通知が届いた場合は行き違いとなります。ご容赦ください。";


/** 【修正版】 前日リマインド 1 **/
$the_day_before_1 = "";
$the_day_before_1 .= "明日は面談です。別途案内メールを\n";
$the_day_before_1 .= "登録アドレス宛に送信しています\n";
$the_day_before_1 .= "形式・場所と時間を必ずご確認下さい\n";
$the_day_before_1 .= "【お問い合わせ先】\n";
$the_day_before_1 .= "ＮＸキャリアロード㈱EC営業部\n";
$the_day_before_1 .= "TEL：050-3819-7869\n";
$the_day_before_1 .= "MAIL:shutokenec@careerroad.co.jp\n";



$the_day_before_1_mail = "";
$the_day_before_1_mail .= "{{name}}様\n";
$the_day_before_1_mail .= "\n";
$the_day_before_1_mail .= "NXキャリアロード株式会社\n";
$the_day_before_1_mail .= "採用担当です。\n";
$the_day_before_1_mail .= "下記日程で面接を設定しました。\n";
$the_day_before_1_mail .= "面接日時：{{interview_date}}\n";
$the_day_before_1_mail .= "面接方式：{{interview_type}}\n";
$the_day_before_1_mail .= "{{interview_venue_address}}\n";

$the_day_before_1_mail .= "\n";
$the_day_before_1_mail .= "面接案内URL：\n";
$the_day_before_1_mail .= "{{url}}\n";
$the_day_before_1_mail .= "\n";

$the_day_before_1_mail .= "日程変更、キャンセルは\n";
$the_day_before_1_mail .= "下記お問合せ先からご連絡ください。\n";
$the_day_before_1_mail .= "\n";
$the_day_before_1_mail .= "【持物】\n";
$the_day_before_1_mail .= "・現住所の確認できる身分証明書\n";
$the_day_before_1_mail .= "・ボールペン\n";
$the_day_before_1_mail .= "※学生の方→学生証\n";
$the_day_before_1_mail .= "※外国籍の方→在留カード、パスポート\n";
$the_day_before_1_mail .= "\n";
$the_day_before_1_mail .= "【服装】\n";
$the_day_before_1_mail .= "私服で結構です。\n";
$the_day_before_1_mail .= "【事前入力】\n";
$the_day_before_1_mail .= "◇下記URLからアクセスして\n";
$the_day_before_1_mail .= "　面接日までに各項目のご入力をお願い致します。\n";
$the_day_before_1_mail .= "URL：\n";
$the_day_before_1_mail .= "https://forms.gle/fxuEq65NURxuJmiv9\n";
$the_day_before_1_mail .= "\n";
$the_day_before_1_mail .= "{{name}}様 のご来社を心待ちにしております。\n";
$the_day_before_1_mail .= "\n";
$the_day_before_1_mail .= "【注意】\n";
$the_day_before_1_mail .= "本メールは自動配信の為\n";
$the_day_before_1_mail .= "お問合せは下記連絡先までお願いします。\n";
$the_day_before_1_mail .= "\n";
$the_day_before_1_mail .= "【お問い合わせ先】\n";
$the_day_before_1_mail .= "ＮＸキャリアロード㈱EC営業部\n";
$the_day_before_1_mail .= "TEL：050-3819-7869\n";
$the_day_before_1_mail .= "MAIL:shutokenec@careerroad.co.jp\n";



/** 【修正版】 前日リマインド 2 **/
$the_day_before_2 = "※ご都合が悪くなった場合など、日時変更は以前お送りした「※面接をキャンセルする場合はこちらから」から可能です。";

/** 不合格 */
// $failure = "アンケート結果をご確認ください。\n";
// $failure .= "{{url}}";
$failure = "株式会社XXXXでございます。\n\n";
$failure .= "この度は、弊社の採用選考をお受け頂きありがとうございました。\n\n";
$failure .= "慎重に検討した結果、ご希望に添いかねることとなりました。\n\n";
$failure .= "大変恐縮ですが、何卒ご了承頂けるように宜しくお願い致します。\n\n";
$failure .= "今後のご活躍を心よりお祈り申し上げます。";

/** 店舗日程調整のメール文言 */
$forStoreAdjust = 'こちらの店舗に応募がありました。<br>';
$forStoreAdjust .= '下記のURLをインターネットエクスプローラーに貼り付けて面接の日程調整を行ってください。<br>';
$forStoreAdjust .= '<br>';
$forStoreAdjust .= '日程調整はこちら↓↓<br>';
$forStoreAdjust .= '{{url}}<br>';
$forStoreAdjust .= '※応募者を放置するとクレームに繋がる可能性があります。<br>';
$forStoreAdjust .= '※また、時間が経つと選択できる日程が少なくなりますので、早めに回答してください。<br>';


/** 面接の合否確認文言 */
$forStorePassFail = '下記のURLから応募者の合否をご回答ください。<br>';
$forStorePassFail .= '回答結果は管理画面に反映されます。<br>';
$forStorePassFail .= '{{url}}<br>';


$checkRecruitContinue = '採用継続確認用のメールです。<br>';
$checkRecruitContinue .= '下記のURLから採用を継続する職種の確認と、<br>';
$checkRecruitContinue .= '面接可能日時の選択を行ってください。<br>';
$checkRecruitContinue .= '{{url}}<br>';
$checkRecruitContinue .= '※こちらのメールは送信専用です。';

/**
 * 店舗用面接確定メール、Googleカレンダーの予定
 */
$schedule_description  = '{{name}} 様'.PHP_EOL;
$schedule_description .= ''.PHP_EOL;
$schedule_description .= '電話番号：{{tel}}'.PHP_EOL;
$schedule_description .= 'メールアドレス：{{mail}}'.PHP_EOL;
$schedule_description .= ''.PHP_EOL;
$schedule_description .= '開始日時：{{start}}'.PHP_EOL;
$schedule_description .= '終了日時：{{end}}'.PHP_EOL;
$schedule_description .= ''.PHP_EOL;
$schedule_description .= '求人原稿：'.PHP_EOL;
$schedule_description .= '{{job_detail}}'.PHP_EOL;
$schedule_description .= ''.PHP_EOL;
$schedule_description .= '入室先 URL：'.PHP_EOL;
$schedule_description .= '{{where_by}}'.PHP_EOL;
$schedule_description .= ''.PHP_EOL;
$schedule_description .= '履歴書：'.PHP_EOL;
$schedule_description .= '{{resume_link}}'.PHP_EOL;
$schedule_description .= ''.PHP_EOL;
$schedule_description .= '職務経歴書：'.PHP_EOL;
$schedule_description .= '{{workhistory_file_link}}'.PHP_EOL;
$schedule_description .= ''.PHP_EOL;
$schedule_description .= '面接後、こちらのURLから合否判定をしてください。'.PHP_EOL;
$schedule_description .= '{{result_urls}}'.PHP_EOL;

// 
$scheduleDescription  = '{{name}} 様'.PHP_EOL;
$scheduleDescription .= ''.PHP_EOL;
$scheduleDescription .= '電話番号：{{tel}}'.PHP_EOL;
$scheduleDescription .= 'メールアドレス：{{mail}}'.PHP_EOL;
$scheduleDescription .= ''.PHP_EOL;
$scheduleDescription .= '開始日時：{{start}}'.PHP_EOL;
$scheduleDescription .= '終了日時：{{end}}'.PHP_EOL;
$scheduleDescription .= ''.PHP_EOL;
$scheduleDescription .= '面接会場：'.PHP_EOL;
$scheduleDescription .= '{{interview_venue}}'.PHP_EOL;
$scheduleDescription .= ''.PHP_EOL;
$scheduleDescription .= '{{interview_meet_url}}';
// $scheduleDescription .= '求人原稿：'.PHP_EOL;
// $scheduleDescription .= '{{job_detail}}'.PHP_EOL;
// $scheduleDescription .= ''.PHP_EOL;
// $scheduleDescription .= '入室先 URL：'.PHP_EOL;
// $scheduleDescription .= '{{where_by}}'.PHP_EOL;
// $scheduleDescription .= ''.PHP_EOL;
// $scheduleDescription .= '履歴書：'.PHP_EOL;
// $scheduleDescription .= '{{resume_link}}'.PHP_EOL;
// $scheduleDescription .= ''.PHP_EOL;
// $scheduleDescription .= '職務経歴書：'.PHP_EOL;
// $scheduleDescription .= '{{workhistory_file_link}}'.PHP_EOL;
// $scheduleDescription .= ''.PHP_EOL;
// $scheduleDescription .= '面接後、こちらのURLから合否判定をしてください。'.PHP_EOL;
// $scheduleDescription .= '{{result_urls}}'.PHP_EOL;

/**
 * 店舗用面接日程調整メール
 */
$schedule_adjustment  = '【至急対応】'.PHP_EOL;
$schedule_adjustment .= '応募者から日程調整の依頼がありました。'.PHP_EOL;
$schedule_adjustment .= '面接の日程調整を行ってください。'.PHP_EOL;
$schedule_adjustment .= ''.PHP_EOL;
$schedule_adjustment .= '応募者希望日時：'. "\n" . '{{schedule_adjust_dates}}'.PHP_EOL;
// $schedule_adjustment .= '応募者ID：'.'{{consumer_id}}'.PHP_EOL;
$schedule_adjustment .= '応募者氏名：'.'{{consumer_name}}'.PHP_EOL;
$schedule_adjustment .= ''.PHP_EOL;
$schedule_adjustment .= '日程調整はこちら'.PHP_EOL;
$schedule_adjustment .= '{{url}}'.PHP_EOL;
$schedule_adjustment .= ''.PHP_EOL;
$schedule_adjustment .= '※応募者を放置するとクレームにつながる可能性があります。'.PHP_EOL;
$schedule_adjustment .= '※また、時間が経つと選択できる日程が少なくなりますので、早めに回答してください。'.PHP_EOL;

/** 
 * 応募者新規登録時メール
 */
// 初回登録
$add_consumer_mail  = "ご応募頂きましてありがとうございます。\n";
$add_consumer_mail .= "下記URLよりweb選考にお進みください。\n\n";
$add_consumer_mail .= "{{url}}\n\n";
$add_consumer_mail .= "※ このメールアドレスは送信専用です。本メールに直接返信いただいても回答できませんのでご注意ください。\n";
// 2回目以降
$add_consumer_mail_duplicate  = "\nご応募ありがとうございます。\n\n";
$add_consumer_mail_duplicate .= "送信済みのアンケートから回答お願いします。";


$failure_black_list  = "この度は、弊社の求人にご応募いただき、誠にありがとうございました。\n\n";
$failure_black_list .= "頂戴しました履歴書をもとに選考を進めさせていただきましたが、";
$failure_black_list .= "残念ながらご希望に沿えない結果となりました。\n";

$failure_black_list_2  = "限られた採用枠に対して多数のご応募をいただいており、慎重に選考を進め、";
$failure_black_list_2 .= "判断させていただいた結果でございます。\n\n";
$failure_black_list_2 .= "誠に恐縮ではございますが、何卒ご理解の程お願い申し上げます。";


return [
    'new'                         => $new,
    'new_mail'                    => $new_mail,
    'second'                      => $second,
    'infoDuplicate'               => $infoDuplicate,
    'applicationEnd'              => $applicationEnd,
    'pass'                        => $pass,
    'pass_mail'                   => $pass_mail,
    'failure'                     => $failure,
    'the_day_before'              => $theDayBefore,
    'forStoreAdjust'              => $forStoreAdjust,
    'forStorePassFail'            => $forStorePassFail,
    'checkRecruitContinue'        => $checkRecruitContinue,
    'schedule_description'        => $schedule_description,
    'schedule_adjustment'         => $schedule_adjustment,
    'add_consumer_mail'           => $add_consumer_mail,
    'add_consumer_mail_duplicate' => $add_consumer_mail_duplicate,
    'the_day_two_days_before'     => $the_day_two_days_before,
    'the_day_before_1'            => $the_day_before_1,
    'the_day_before_1_mail'      => $the_day_before_1_mail,
    'the_day_before_2'            => $the_day_before_2,
    'failure_black_list'          => $failure_black_list,
    'failure_black_list_2'        => $failure_black_list_2,
    'scheduleDescription'         => $scheduleDescription,
];


