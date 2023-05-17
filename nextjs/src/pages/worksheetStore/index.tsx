import type { NextPage } from 'next'
import React, { createContext, useState, Dispatch, SetStateAction, useEffect } from "react";
import { useForm, Controller, SubmitHandler } from "react-hook-form";
import { Grid, makeStyles, styled } from '@material-ui/core'
import Step from '@material-ui/core/Step';
import FormHeader from '@/components/Layouts/Header/FormHeader';
import { StoreAnswerData, WorksheetStoreProps } from '@/types/worksheet'
import axios from '@/lib/axios'
import Loading from '@/components/Layouts/Ui/Loading';

const CustomGrid = styled(Grid)({
  backgroundColor: '#d8edfb'
})
const CustomStep = styled(Step)({
  '& .MuiStepIcon-root.MuiStepIcon-active': {
    color: '#3765a5',
  }
})

interface IFormInput {
}
interface IPostResponse {
}

export const UserInputData = createContext({} as {
  currentState: StoreAnswerData
  setCurrentState: Dispatch<SetStateAction<StoreAnswerData>>
});

const WorksheetStorePage: NextPage = (props: { data: WorksheetStoreProps }) => {
  const WorksheetStoreData = props.data;
  const consumerId = WorksheetStoreData.consumerId;
  const name = WorksheetStoreData.name;
  const atsId = WorksheetStoreData.ats_id;
  const tel = WorksheetStoreData.tel;
  const mail = WorksheetStoreData.mail;
  const anotherSchedule = WorksheetStoreData.adjustSchedules;

  // アンケート回答情報
  const worksheetAnswer = WorksheetStoreData.worksheetAnswer;
  const jobtype = worksheetAnswer.jobtype;
  const interviewPlace = worksheetAnswer.interviewPlace;
  // const interviewVenue = WorksheetStoreData.interview_venue;
  console.log(WorksheetStoreData);

  const [loading, setLoading] = useState(false);
  const [checkedItems, setCheckedItems] = useState("")
  const [isAnotherAdjust, setAnotherAdjust] = useState(false);
  useEffect(() => {
    // 職務経歴書登録フォームの表示
    setTimeout(() => {
      if (checkedItems == "self_adjustment") {
        setAnotherAdjust(true)
      } else {
        setAnotherAdjust(false)
      }
    }, 10);
  }, [checkedItems])
  const handleChange = e => {
    console.log(e.target.value)
    setCheckedItems(e.target.value)
  }

  const {
    register,
    control,
    handleSubmit,
    formState: { errors },
    getValues,
  } = useForm<StoreAnswerData>({
    defaultValues: {
      // pref: selectedPref
    }
  });

  // 選択した職種によって次ページで表示させるパターンを変更する
  const [currentState, setCurrentState] = React.useState({
    consumerId: consumerId,
    scheduleType: "",
    interviewDate: "",
  });

  const csrf = () => axios.get(process.env.NEXT_PUBLIC_BACKEND_URL + '/sanctum/csrf-cookie');

  const onApiSubmit: SubmitHandler<StoreAnswerData> = async (data) => {
    if (!confirm('送信します。よろしいですか？')) {
      return;
    }
    console.log('========onApiSubmit========');
    setLoading(true);
    const requestUrl = process.env.NEXT_PUBLIC_BACKEND_URL + '/api/store-answer';
    const inputdata = getValues();
    const scheduleType = inputdata.scheduleType;
    const interviewDate = inputdata.interviewDate;
    const answerData: IFormInput = {
      consumerId: consumerId,
      scheduleType: scheduleType,
      interviewDate: interviewDate
    }
    await csrf()
    console.log('answerData!!!!!');
    console.log(answerData);
    axios.post<IPostResponse>(
      requestUrl,
      {
        consumerId: consumerId,
        answerData: answerData,
      },
    ).then(() => {
      console.log('送信完了');
      setLoading(false);
      // props.handleNext();
    }).catch((error) => {
      alert("送信に失敗しました。\n大変申し訳ございませんが、しばらくしてからもう一度お試しください。")
      console.log(error);
      setLoading(false);
    });
    return;
  }


  return (
    <>
      {loading ? <Loading /> : <br />}
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
            <FormHeader />
          </Grid>
          <CustomGrid item lg={3} sm={3} xs={1} />
          <CustomGrid item lg={6} sm={6} xs={12}>
            <UserInputData.Provider value={{ currentState, setCurrentState }}>
              <div className="container p-0">

                <div className="w-full mt-5 p-4 bg-white border border-gray-200 rounded-lg sm:p-8">
                  {/* <div className="flex items-center justify-between mb-4">
                    <h5 className="text-xl font-bold leading-none text-gray-900 ">Latest Customers</h5>
                    <a href="#" className="text-sm font-medium text-blue-600 hover:underline ">
                      View all
                    </a>
                  </div> */}
                  <div className="flow-root">
                    <h2 className="text-sky-600 text-lg mb-1 font-bold title-font text-left">
                      応募者情報
                    </h2>
                    <ul role="list" className="divide-y divide-gray-200">
                      <li className="py-3 sm:py-4">
                        <div className="flex items-center space-x-4">
                          <div className="flex-1 min-w-0">
                            <p className="text-sm font-medium text-gray-900 truncate ">
                              名前
                            </p>
                          </div>
                          <div className="inline-flex items-center text-base font-semibold text-gray-900 ">
                            {name}
                          </div>
                        </div>
                      </li>
                      <li className="py-3 sm:py-4">
                        <div className="flex items-center space-x-4">
                          <div className="flex-1 min-w-0">
                            <p className="text-sm font-medium text-gray-900 truncate ">
                              ジョブオプ応募ID
                            </p>
                          </div>
                          <div className="inline-flex items-center text-base font-semibold text-gray-900 ">
                            {atsId}
                          </div>
                        </div>
                      </li>
                      <li className="py-3 sm:py-4">
                        <div className="flex items-center space-x-4">
                          <div className="flex-1 min-w-0">
                            <p className="text-sm font-medium text-gray-900 truncate ">
                              電話番号
                            </p>
                          </div>
                          <div className="inline-flex items-center text-base font-semibold text-gray-900 ">
                            {tel}
                          </div>
                        </div>
                      </li>
                      <li className="py-3 sm:py-4">
                        <div className="flex items-center space-x-4">
                          <div className="flex-1 min-w-0">
                            <p className="text-sm font-medium text-gray-900 truncate ">
                              メールアドレス
                            </p>
                          </div>
                          <div className="inline-flex items-center text-base font-semibold text-gray-900 ">
                            {mail}
                          </div>
                        </div>
                      </li>
                      <li className="py-3 sm:py-4">
                        <div className="flex items-center space-x-4">
                          <div className="flex-1 min-w-0">
                            <p className="text-sm font-medium text-gray-900 truncate ">
                              希望職種
                            </p>
                          </div>
                          <div className="inline-flex items-center text-base font-semibold text-gray-900 ">
                            {jobtype}
                          </div>
                        </div>
                      </li>
                      <li className="py-3 sm:py-4">
                        <div className="flex items-center space-x-4">
                          <div className="flex-1 min-w-0">
                            <p className="text-sm font-medium text-gray-900 truncate ">
                              希望面接場所
                            </p>
                          </div>
                          <div className="inline-flex items-center text-base font-semibold text-gray-900 ">
                            {interviewPlace}
                          </div>
                        </div>
                      </li>
                    </ul>
                  </div>
                </div>

                <form onSubmit={handleSubmit(onApiSubmit)}>
                  <div className="formBody">
                    <div className="flex w-full flex-col
          justify-center rounded-lg p-5 my-5 bg-white">
                      <div className="text-center">
                        <div className="relative my-2">
                          <div className="flex text-center">
                            <h2 className="text-sky-600 text-lg mb-1 font-bold title-font text-left">
                              対応
                            </h2>
                            <div
                              className="ml-4 text-xs inline-flex 
                    items-center font-bold leading-sm uppercase px-3 pb-0 
                    bg-red-200 text-red-700 rounded-full">
                              必須
                            </div>
                          </div>
                        </div>
                      </div>
                      <div className="grid w-full gap-1 grid-cols-1 py-3">
                        <div className="flex items-center pl-4 border border-gray-200 rounded ">
                          <input
                            id="bordered-radio-1"
                            className="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500"
                            type="radio"
                            // defaultValue="no"
                            defaultValue="decide_date"
                            defaultChecked
                            {...register("scheduleType", {
                              'required': '※対応を選択してください。',
                            })}
                            onChange={handleChange}
                          />
                          <label
                            className="w-full py-4 ml-2 text-sm font-medium text-gray-900"
                            htmlFor="bordered-radio-1"
                          >
                            <span>応募者の面接希望日を選択する</span>
                          </label>
                        </div>
                        <div className="flex items-center pl-4 border border-gray-200 rounded ">
                          <input
                            id="bordered-radio-2"
                            className="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500"
                            type="radio"
                            defaultValue="self_adjustment"
                            {...register("scheduleType", {
                              'required': '※対応を選択してください。',
                            })}
                            onChange={handleChange}
                          />
                          <label
                            className="w-full py-4 ml-2 text-sm font-medium text-gray-900"
                            htmlFor="bordered-radio-2">
                            <span>電話・メールで個別に応募者と調整する</span>
                          </label>
                        </div>
                        {!isAnotherAdjust ? <>
                          <div className="py-3">
                            <div className='flex'>
                              <h2 className="text-sky-600 text-lg mb-1 font-bold title-font text-left">応募者希望日</h2>
                              <div
                                className="ml-4 text-xs inline-flex 
                              items-center font-bold leading-sm uppercase px-3 pb-0 
                              bg-red-200 text-red-700 rounded-full">
                                必須
                              </div>
                            </div>
                            <p className="text-sm text-red-400 font-bold">{errors.interviewDate?.message}</p>
                            <div className="p-3">
                              <select
                                className=" bg-gray-100 rounded  
                              focus:border-sky-500 focus:ring-2 
                              focus:ring-sky-200 text-xs outline-none border-transparent
                              text-gray-700 py-2 px-2 leading-8 transition-colors duration-200 ease-in-out
                              w-full my-3"
                                {...register("interviewDate", {
                                  'required': '※面接日時を選択してください。',
                                })}

                              >
                                <option value="" data-date="0">日程を選択してください。</option>
                                {
                                  anotherSchedule.map((schedule, index) => (
                                    <option value={schedule} key={index}>
                                      {schedule}</option>
                                  ))
                                }
                              </select>
                              <div className="text-sm text-red-400 font-bold">
                                <p>※直近36時間以内の選択肢は表示されません。</p>
                                <p>※選択肢が表示されない・希望の日時が存在しない場合は、</p>
                                <p>「電話・メールで調整する」を選択して送信ボタンを押しください。</p>
                              </div>
                            </div>
                          </div>
                        </> : <></>}
                      </div>
                      <div className="mt-5 text-center">
                        <div className="col-6">
                          <button type="submit"
                            className="text-white bg-sky-600 hover:bg-sky-800 focus:ring-4 
                        focus:outline-none focus:ring-blue-300 
                        font-bold rounded-lg text-lg 
                        px-5 py-3 text-center inline-flex items-center
                        border-b-4 border-sky-800
                        ">
                            送信する
                          </button>
                        </div>
                      </div>

                    </div>
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
    + `/worksheetStore?csid=${csid}`
  const response = await fetch(encodeURI(apiRequestUrl));
  return {
    props: {
      data: await response.json()
    }
  }
};

export default WorksheetStorePage;
