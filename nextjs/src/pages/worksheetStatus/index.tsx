import type { NextPage } from 'next'
import React, {
  createContext, useState,
  Dispatch, SetStateAction
} from "react";
import { Grid, makeStyles, styled } from '@material-ui/core'
import Step from '@material-ui/core/Step';
import StatusHeader from '@/components/Layouts/Header/StatusHeader';
import WorksheetStatus from '@/components/Layouts/Worksheet/WorksheetStatus';
import { UserData, StatusProps } from '@/types/worksheet'

const CustomGrid = styled(Grid)({
  // backgroundColor: '#d8edfb'
  backgroundColor: '#E9F3CD'
})
const CustomStep = styled(Step)({
  '& .MuiStepIcon-root.MuiStepIcon-active': {
    color: '#3765a5',
  }
})

export const UserInputData = createContext({} as {
  currentState: UserData
  setCurrentState: Dispatch<SetStateAction<UserData>>
});

const StatusPage: NextPage = (props: StatusProps) => {
  const statusData = props.data;
  console.log(statusData);
  const startDate = statusData.start_date;
  const endDate = statusData.end_date;
  const interviewVenue = statusData.interview_venue;
  const googleMeetUrls = statusData.google_meet_urls;
  const venueAddress = statusData.venue_address;
  const isFace = venueAddress != '' ? true : false;
  const isWeb = googleMeetUrls.length > 0 ? true : false;


  const [loading, setLoading] = useState(false);

  // 選択した職種によって次ページで表示させるパターンを変更する
  const [currentState, setCurrentState] = React.useState({
    name: "",
    birthYear: "",
    birthMonth: "",
    birthDay: "",
    jobtype: "",
    first_choice_select: "",
    second_choice_select: "",
    third_choice_select: "",
    interviewPlace: "",
    motivation: "",
    StepInterviewSchedule: [],
    StepInterviewAnotherSchedule: [],
    isAnotherAdjust: false,
    targetSchedules: [],
  });


  return (
    <>
      <div className="bg-white">
        <Grid container>
          <Grid item xs={12}>
            <div className="grid w-full gap-0 grid-cols-1 text-center">
              <div className="p-1">
                <img
                  src="/img/N5526772.jpeg"
                  style={{ width: '150px' }}
                  className="m-auto"
                />
              </div>
            </div>
          </Grid>

          <Grid item xs={12} >
            <StatusHeader />
          </Grid>
          <CustomGrid item lg={3} sm={3} xs={1} />
          <CustomGrid item lg={6} sm={6} xs={12}>
            <UserInputData.Provider value={{ currentState, setCurrentState }}>
              <div className="container p-0">
                <form>
                  <div className="formBody">
                    <div className="flex w-full flex-col
          justify-center rounded-lg p-5 my-5 bg-white">
                      <div className="text-center">
                        <div className="relative my-2">
                          <div className="text-center">
                            <h2 className="text-[#8cc404] text-lg mb-1 font-bold title-font text-center">
                              面接日時
                            </h2>
                            <p className='py-3 font-bold'>
                              {startDate} ~ {endDate}
                            </p>
                            <h2 className="text-[#8cc404] text-lg mb-1 mt-3 font-bold title-font text-center">
                              面接会場
                            </h2>
                            <p className='py-3 font-bold'>
                              {interviewVenue}
                            </p>
                            {isFace ? <>
                              <p className='py-3 font-bold'>
                                {venueAddress}
                              </p>
                            </> : <></>
                            }
                            {isWeb ? <>
                              <h2 className="text-[#8cc404] text-lg mb-1 mt-3 font-bold title-font text-center">
                                Web面接URL
                              </h2>
                              <p className='py-3 font-bold'>
                                <a className='text-sky-500' href={googleMeetUrls[0]} > こちらからご参加ください </a>
                              </p>
                            </> : <></>
                            }
                            <h2 className="text-[#8cc404] text-lg mb-1 mt-3 font-bold title-font text-center">
                              持ち物
                            </h2>
                            <div className='text-center grid gap-1 grid-cols-1 mx-auto w-full'>
                              <p className='py-1 font-bold'>
                                ・現住所の確認できる身分証明書
                              </p>
                              <p className='py-1 font-bold'>
                                ・ボールペン
                              </p>
                              <p className='py-1 font-bold'>
                                ※学生の方→学生証
                              </p>
                              <p className='py-1 font-bold'>
                                ※外国籍の方→在留カード、パスポート
                              </p>
                              <h2 className="text-[#8cc404] text-lg mb-1 mt-3 font-bold title-font text-center">
                                服装
                              </h2>
                              <p className='py-3 font-bold text-center'>
                                私服で結構です。
                              </p>
                              <h2 className="text-[#8cc404] text-lg mb-1 mt-3 font-bold title-font text-center">
                                お問い合わせ先
                              </h2>
                              <p className='py-1 font-bold'>NXキャリアロード㈱EC営業部</p>
                              <p className='py-1 font-bold'>TEL: <a className='text-sky-500' href="tel:05038197869">050-3819-7869</a></p>
                              <p className='py-1 font-bold'>MAIL: <a className='text-sky-500' href="email:shutokenec@careerroad.co.jp">shutokenec@careerroad.co.jp</a></p>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    {/* <div className="card p-5">
                      <p className="text-[#27145d] text-base mb-1 font-bold title-font text-center">
                        ご不明な点がございましたら、</p>
                      <p className="text-[#27145d] text-base mb-1 font-bold title-font text-center">
                        下記までお問い合わせくださいませ。</p>
                      <p className="text-[#27145d] text-base mb-1 font-bold title-font text-center">
                        よろしくお願いいたします。</p>
                      <ul className="card__inputField">
                        <li className="text-[#27145d] text-base mb-1 font-bold title-font text-center">
                        </li>
                      </ul>
                    </div> */}
                  </div >
                </form >
              </div >
            </UserInputData.Provider>
          </CustomGrid>
          <CustomGrid item lg={3} sm={3} xs={1} />
        </Grid>
      </div>
    </>
  )
}

export const getServerSideProps = async (context) => {
  const csid = context.query.csid;
  // const cskey = context.query.cskey;
  const apiRequestUrl = process.env.NEXT_PUBLIC_BACKEND_URL_API
    + `/worksheetStatus?csid=${csid}`
  const response = await fetch(encodeURI(apiRequestUrl));
  return {
    props: {
      data: await response.json()
    }
  }
};

export default StatusPage;
