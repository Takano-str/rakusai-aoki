import React, { createContext, useState, useEffect, useContext } from "react";
import { useForm, Controller, SubmitHandler } from "react-hook-form";
import { Button, MenuItem, styled } from "@material-ui/core";
import { UserInputData } from "@/pages/worksheet/index";
import { UserData, Schedule, WorksheetProps, WorksheetData } from '@/types/worksheet'
import axios from '@/lib/axios'
import Loading from '@/components/Layouts/Ui/Loading';
// import Axios from 'axios'
// import { parseCookies, setCookie, destroyCookie } from 'nookies'
// import Cookies from "js-cookie";
// import { parseCookies, setCookie, destroyCookie } from 'nookies'
// import { Scheduler } from "timers/promises";
// import FormHeader from "../../FormHeader";
// import FormHeaderContact from "../../FormHeaderContact";

interface WorksheetSecondProps {
  consumerId: string
  schedules: Schedule[]
  anotherDateArray: string[]
  setPattern: any
  handleNext: any
  handleBack: any
}

interface IPostRequest {
  emil: string
  password: string
}

interface IPostResponse {
  id: number
  token: string
}

interface IFormInput {
  name: string
  birthYear: string
  birthMonth: string
  birthDay: string
  jobtype: string
  interviewPlace: string
  motivation: string

  StepInterviewSchedule: string[]
  StepInterviewAnotherSchedule: string[]

  isAnotherAdjust: boolean

  // first_choice_select: string
  // second_choice_select: string
  // third_choice_select: string
}

const WorksheetSecond = (props: WorksheetSecondProps) => {
  useEffect(() => { window.scrollTo(0, 0); }, []);
  const { currentState, setCurrentState } = useContext(UserInputData);
  const consumerId = props.consumerId;
  const schedules = props.schedules;
  const anotherDateArray = props.anotherDateArray;
  const anotherInitList = anotherDateArray.map((anotherDate) => {
    return { 'date': anotherDate.slice(0, -2), 'start': '', 'end': '' }
  });
  const [anotherAdjustDates, setAnotherAdjustDates] = useState(anotherInitList);
  const [checkedItems, setCheckedItems] = useState("")
  const [isAnotherAdjust, setAnotherAdjust] = useState(false);
  const [loading, setLoading] = useState(false);

  const {
    register,
    control,
    handleSubmit,
    formState: { errors },
    getValues,
  } = useForm<UserData>({
    defaultValues: {
      // pref: selectedPref
    }
  });

  useEffect(() => {
    // 職務経歴書登録フォームの表示
    setTimeout(() => {
      if (checkedItems == "yes") {
        setAnotherAdjust(true)
      } else {
        setAnotherAdjust(false)
      }
    }, 10);
  }, [checkedItems])
  const handleChange = e => {
    setCheckedItems(e.target.value)
  }

  const onChangeAnotherDates = (event) => {
    const date = event.target.dataset.date;
    const type = event.target.dataset.type;
    const shapedDate = date.slice(0, -2);
    const time = event.target.value;
    if (type == 'start') {
      setAnotherAdjustDates((prevState) =>
        prevState.map((obj) => (obj.date == shapedDate ? { 'date': obj.date, 'start': time, 'end': obj.end } : obj))
      );
    }
    if (type == 'end') {
      setAnotherAdjustDates((prevState) =>
        prevState.map((obj) => (obj.date == shapedDate ? { 'date': obj.date, 'start': obj.start, 'end': time } : obj))
      );
    }
  }

  let urlArg = new Object;
  let urLparam = location.search.substring(1).split('&');
  for (let i = 0; urLparam[i]; i++) {
    let paramInfo = urLparam[i].split('=');
    urlArg[paramInfo[0]] = paramInfo[1];
  }

  const startSelectTimeList = [
    '10:00', '11:00', '12:00', '13:00', '14:00',
    '15:00', '16:00', '17:00', '18:00', '19:00'
  ];
  const endSelectTimeList = [
    '11:00', '12:00', '13:00', '14:00', '15:00',
    '16:00', '17:00', '18:00', '19:00', '20:00'
  ];
  const weekdays = ['(日)', '(月)', '(火)', '(水)', '(木)', '(金)', '(土)'];

  const getAnotherDates = (anotherAdjustDates) => {
    console.log('getAnotherDates!!!!!!!!!');
    const scheduleDates = [];
    for (const anotherDateObj of anotherAdjustDates) {
      const date = anotherDateObj.date;
      const start = anotherDateObj.start;
      const end = anotherDateObj.end;
      if (start == "" || end == "") {
        continue;
      }
      scheduleDates.push(date + '_' + start + '~' + end);
    }
    return scheduleDates;
  }


  const onSubmit = (action) => {
    console.log('onSubmit!');
    console.log(isAnotherAdjust);
    console.log(anotherAdjustDates);

    if (action === 'back') {
      props.handleBack();
    } else if (isAnotherAdjust) {
      // console.log('another_adjust!');
      // if (!anotherDatesValidation(anotherAdjustDates)) {
      //   console.log('error!!!!!');
      //   return;
      // }
      // props.handleNext();
      // const anotherDates = getAnotherDates(anotherAdjustDates);
      // const anotherData = {
      //   store_id: store_id,
      //   // store_group_id: store_group_id,
      //   anotherDates: anotherDates,
      // };
      // console.log(anotherDates);
      // setCurrentState({
      //   ...currentState,
      //   "StepInterviewAnotherSchedule": anotherData
      // })
    } else {
      console.log('schedule on!')
      // props.handleNext()
      // const data = getValues()
      // setCurrentState({
      //   ...currentState,
      //   "StepInterviewSchedule": data
      // })
    }
  }

  const csrf = () => axios.get(process.env.NEXT_PUBLIC_BACKEND_URL + '/sanctum/csrf-cookie');

  const onApiSubmit: SubmitHandler<UserData> = async (data) => {
    setLoading(true);
    const requestUrl = process.env.NEXT_PUBLIC_BACKEND_URL + '/api/consumer-answer';
    const inputdata = getValues();
    console.log('========onApiSubmit========');
    console.log(requestUrl);
  
  let anotherDates = [];
    // 店舗日程調整
    if (isAnotherAdjust) {
      console.log('another_adjust!');
      if (!anotherDatesValidation(anotherAdjustDates)) {
        console.log('error!!!!!');
        return;
      }
      anotherDates = getAnotherDates(anotherAdjustDates);
      // props.handleNext();
      // const anotherData = {
      //   store_id: store_id,
      //   anotherDates: anotherDates,
      // };
      // setCurrentState({
      //   ...currentState,
      //   "StepInterviewAnotherSchedule": anotherData
      // })
    } else {
      // 通常の回答
      console.log('schedule on!')
      // props.handleNext()
      // const data = getValues()
      // setCurrentState({
      //   ...currentState,
      //   "StepInterviewSchedule": data
      // })
    }
    console.log(anotherDates);

    const answerData: IFormInput = {
      name: currentState.name,
      birthYear: currentState.birthYear,
      birthMonth: currentState.birthMonth,
      birthDay: currentState.birthDay,
      jobtype: currentState.jobtype,
      interviewPlace: currentState.interviewPlace,
      motivation: currentState.motivation,
      StepInterviewSchedule: [
        inputdata.first_choice_select,
        inputdata.second_choice_select,
        inputdata.third_choice_select,
      ],
      StepInterviewAnotherSchedule: anotherDates,
      isAnotherAdjust: isAnotherAdjust,
    }
    console.log(answerData);

    await csrf()
    console.log('api request!!!');
    // console.log(Cookies.get("XSRF-TOKEN"));
    // const axios = Axios.create({
    //   baseURL: process.env.NEXT_PUBLIC_BACKEND_URL,
    //   headers: {
    //       'X-Requested-With': 'XMLHttpRequest',
    //       // 'XSRF-TOKEN': Cookies['XSRF-TOKEN']
    //   },
    //   withCredentials: true,
    // })

    axios.post<IPostResponse>(
      requestUrl,
      {
        consumerId: consumerId,
        answerData: answerData,
      },
    ).then(() => {
      console.log('送信完了');
      setLoading(false);
      props.handleNext();
    }).catch((error) => {
      alert("送信に失敗しました。\n大変申し訳ございませんが、しばらくしてからもう一度お試しください。")
      console.log(error);
      setLoading(false);
    });
  };




  
  return <>
    <div className="container p-0">
      {loading ? <Loading /> : <br />}
      {/* <form onSubmit={handleSubmit(onSubmit)}> */}
      <form onSubmit={handleSubmit(onApiSubmit)}>
        <div className="formBody">
          <div className="flex w-full flex-col
          justify-center rounded-lg p-5 my-5 bg-white">

            <div className="text-left">
              <div className="relative my-2">
                {!isAnotherAdjust ? <>
                  <div className="flex mb-1">
                    <h2 className="text-[#27145d] text-sm mb-1 font-bold title-font">
                      下記より面接希望日を選択してください。
                    </h2>
                    <div
                      className="ml-4 text-xs inline-flex 
                    items-center font-bold leading-sm uppercase px-3 pb-0 
                    bg-red-200 text-red-700 rounded-full">
                      必須
                    </div>
                  </div>
                  <p className="text-sm text-red-400 font-bold">{errors.first_choice_select?.message}</p>
                  <p className="text-sm text-red-400 font-bold">{errors.second_choice_select?.message}</p>
                  <p className="text-sm text-red-400 font-bold">{errors.third_choice_select?.message}</p>

                  <div className="form_choices">
                    <div className="grid w-full gap-1 grid-cols-1">
                      <div className="flex">
                        {(() => {
                          return (
                            <select
                              {...register("first_choice_select", {
                                'required': '※第一希望日を入力してください。',
                              })}
                              className=" bg-gray-100 rounded  
                              focus:border-sky-500 focus:ring-2 
                              focus:ring-sky-200 text-xs outline-none border-transparent
                              text-gray-700 py-2 px-2 leading-8 transition-colors duration-200 ease-in-out
                              w-full mt-3"
                              name="first_choice_select"
                            >
                              <option data-id="0" value="">第一希望日▼</option>
                              {
                                currentState.targetSchedules.map((schedule) => (
                                  <option value={schedule.id} key={schedule.id}>{schedule.start_date}～</option>
                                ))
                              }
                            </select>
                          )
                        })()}
                      </div>
                    </div>
                  </div>

                  <div className="form_choices">
                    <div className="grid w-full gap-1 grid-cols-1">
                      <div className="flex">
                        <select
                          {...register("second_choice_select", {
                            'required': '※第二希望日を入力してください。',
                          })}
                          className=" bg-gray-100 rounded  
                              focus:border-sky-500 focus:ring-2 
                              focus:ring-sky-200 text-xs outline-none border-transparent
                              text-gray-700 py-2 px-2 leading-8 transition-colors duration-200 ease-in-out
                              w-full mt-3"
                          name="second_choice_select"
                        >
                          <option data-id="0" value="">第二希望日▼</option>
                          {
                            currentState.targetSchedules.map((schedule) => (
                              <option value={schedule.id} key={schedule.id}>{schedule.start_date}～</option>
                            ))
                          }
                        </select>
                      </div>
                    </div>
                  </div>

                  <div className="form_choices">
                    <div className="grid w-full gap-1 grid-cols-1">
                      <div className="flex">
                        <select
                          {...register("third_choice_select", {
                            'required': '※第三希望日を入力してください。',
                          })}
                          className=" bg-gray-100 rounded  
                              focus:border-sky-500 focus:ring-2 
                              focus:ring-sky-200 text-xs outline-none border-transparent
                              text-gray-700 py-2 px-2 leading-8 transition-colors duration-200 ease-in-out
                              w-full mt-3"
                          name="third_choice_select"
                        >
                          <option data-id="0" value="">第三希望日▼</option>
                          {
                            currentState.targetSchedules.map((schedule) => (
                              <option value={schedule.id} key={schedule.id}>{schedule.start_date}～</option>
                            ))
                          }
                        </select>
                      </div>
                    </div>
                  </div>

                  <div className="text-center my-5">
                  </div>
                </>
                  : ''}

                {/* ==============その他の日程を選択============== */}
                {isAnotherAdjust ? <>
                  {/* <div className="another_adjust_schedule_form"> */}
                  <div className="flex mb-1">
                    <h2 className="text-[#27145d] text-sm mb-1 font-bold title-font">
                      面接可能な日時を3つ以上選択してください。
                    </h2>
                    <div
                      className="ml-4 text-xs inline-flex 
                    items-center font-bold leading-sm uppercase px-3 pb-0 
                    bg-red-200 text-red-700 rounded-full">
                      必須
                    </div>
                  </div>

                  <div className="form_choices">
                    <div className="grid w-full gap-1 grid-cols-1">
                      <div>
                        <table className="w-full text-sm text-left text-gray-500">
                          <tbody>
                            {
                              anotherDateArray.map((anotherDate, index) => (
                                <tr className="bg-white border-b hover:bg-gray-50" key={index}>
                                  <th>
                                    <span className="h6">
                                      {anotherDate.slice(0, -2)}{weekdays[anotherDate.substr(-1)]}
                                    </span>
                                  </th>
                                  <td>
                                    <ul className="flex">
                                      <li className="grow">
                                        <select
                                          name={'start_datetimepicker_' + index}
                                          data-name={'datetimepicker_' + index}
                                          data-date={anotherDate}
                                          data-type={'start'}
                                          onChange={onChangeAnotherDates}
                                          className=" bg-gray-100 rounded  
                                          focus:border-sky-500 focus:ring-2 
                                          focus:ring-sky-200 text-xs outline-none border-transparent
                                          text-gray-700 py-2 px-2 leading-8 transition-colors duration-200 ease-in-out
                                          w-full my-1"
                                        >
                                          <option className="start" data-loop_count="0" data-time="0" value="">時間を選択</option>
                                          {startSelectTimeList.map((startSelectTime, index) => (
                                            <option value={startSelectTime} key={index}>{startSelectTime}～</option>
                                          ))}
                                        </select>
                                      </li>
                                      <li className="grow-0 py-4">から</li>
                                      <li className="grow">
                                        <select
                                          name={'start_datetimepicker_' + index}
                                          data-name={'datetimepicker_' + index}
                                          data-date={anotherDate}
                                          data-type={'end'}
                                          onChange={onChangeAnotherDates}
                                          className=" bg-gray-100 rounded  
                                          focus:border-sky-500 focus:ring-2 
                                          focus:ring-sky-200 text-xs outline-none border-transparent
                                          text-gray-700 py-2 px-2 leading-8 transition-colors duration-200 ease-in-out
                                          w-full my-1"
                                        >
                                          <option className="end" data-loop_count="0" data-time="0" value="">時間を選択</option>
                                          {endSelectTimeList.map((endSelectTime, index) => (
                                            <option value={endSelectTime} key={index}>{endSelectTime}～</option>
                                          ))}
                                        </select>
                                      </li>
                                      <li className="grow-0 py-4">まで</li>
                                      {/* <span>まで</span> */}
                                    </ul>
                                  </td>
                                </tr>
                              ))
                            }
                          </tbody>
                        </table>
                      </div>
                    </div>
                    <div className="text-center my-5">
                    </div>
                  </div>
                  {/* </div> */}
                </>
                  : <br />}


                {/* <div className="grid w-full gap-1 grid-cols-2">
                  <div className="flex items-center pl-4 border border-gray-200 rounded ">
                    <input
                      id="bordered-radio-1"
                      className="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500"
                      name="schedule_type"
                      type="radio"
                      defaultValue="no"
                      defaultChecked
                      onChange={handleChange} />
                    <label
                      className="w-full py-4 ml-2 text-sm font-medium text-gray-900"
                      htmlFor="bordered-radio-1"
                    >
                      <span>面接日程を選択する</span>
                    </label>
                  </div>
                  <div className="flex items-center pl-4 border border-gray-200 rounded ">
                    <input
                      id="bordered-radio-2"
                      className="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500"
                      name="schedule_type"
                      type="radio"
                      defaultValue="yes"
                      onChange={handleChange} />
                    <label
                      className="w-full py-4 ml-2 text-sm font-medium text-gray-900"
                      htmlFor="bordered-radio-2">
                      <span>自分の都合のつく日程を登録する</span>
                    </label>
                  </div>
                </div> */}
              </div>
            </div>




          </div>
          <div className="grid w-full gap-1 grid-cols-2">
            <div className="mt-5 mb-10 text-right">
              <div className="p-2">
                {/* <Button variant="contained" color="primary" onClick={props.handleBack}>
                      戻る
                    </Button> */}
                <button
                  onClick={() => onSubmit("back")}
                  // className="text-white bg-sky-600 hover:bg-sky-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center">
                  className="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-l mr-3">
                  もどる
                </button>
              </div>
            </div>
            <div className="mt-5 mb-10">
              <div className="col-6">
                {/* <Button
                      variant="contained"
                      color="primary"
                      type="submit"
                    >
                      確認
                    </Button> */}
                <button type="submit"
                  className="text-white bg-[#554a7d] hover:bg-[#27145d] focus:ring-4 
                        focus:outline-none focus:ring-blue-300 
                        font-bold rounded-lg text-lg 
                        px-5 py-5 text-center inline-flex items-center
                        border-b-4 border-[#27145d]
                        ">
                  送信する
                </button>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </>
}

const anotherDatesValidation = (anotherDates) => {
  let selectCount = 0;
  let errorMessageList = [];

  for (const anotherDateObj of anotherDates) {
    const date = anotherDateObj.date;
    const start = anotherDateObj.start;
    const end = anotherDateObj.end;
    if (start == '' && end == '') {
      continue;
    }
    if (start != '' && end != '') {
      selectCount++;
      const startNum = Number(start.substr(0, 2));
      const endNum = Number(end.substr(0, 2));
      if (startNum >= endNum) {
        errorMessageList.push(date + 'の時間帯に不備があります。');
      }
    }
    if (start == '' || end == '') {
      errorMessageList.push(date + 'の時間帯に不備があります。');
    }
  }
  if (selectCount < 3) {
    errorMessageList.push('3日以上時間帯を選択してください。');
  }

  // エラーがある場合
  if (errorMessageList.length > 0) {
    const errorMessage = errorMessageList.join('\n');
    alert(errorMessage);
    return false;
  }
  return true;
}

export default WorksheetSecond
