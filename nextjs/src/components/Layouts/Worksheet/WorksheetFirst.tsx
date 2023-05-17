import React, { createContext, useState, useContext } from "react";
import { useForm, Controller } from "react-hook-form";
import { Button, MenuItem, styled } from "@material-ui/core";
import { UserInputData } from "@/pages/worksheet/index";
import { SchedulesForWorksheet } from '@/types/worksheet'

// type UserData = {
//   id: string | null;
//   name: string | null;
// };

// type UserContextType = {
//   user: UserData | null;
//   setUser: (user: UserData) => void;
// };

interface JobDetailProps {
  setPattern: any
  handleNext: any
  assetPath: string
  schedulesForWorksheet: SchedulesForWorksheet
  interviewVenues: string[]
};

// const CustomButton = styled(Button)({
//   backgroundColor: "#3ab27a",
// })

// const interviewPlaceList = [
//   { key: "Web", text: "Web面接", },
//   { key: "本川越", text: "本川越", },
//   { key: "狭山", text: "狭山", },
// ]

const jobtypeList = [
  { key: "川越", text: "川越", },
  { key: "狭山", text: "狭山", },
  { key: "どちらでも可", text: "どちらでも可", },
];
interface IFormInput {
  name: string;
  birthYear: string;
  birthMonth: string;
  birthDay: string;
  jobtype: string;
  interviewPlace: string;
  motivation: string;
}

// export const UserInputData = createContext<UserContextType>;

const WorksheetFirst = (props: JobDetailProps) => {
  // console.log(props)
  const interviewVenues = props.interviewVenues;
  const schedulesForWorksheet = props.schedulesForWorksheet;
  const { currentState, setCurrentState } = useContext(UserInputData);
  const {
    register,
    control,
    handleSubmit,
    formState: { errors },
    getValues,
  } = useForm<IFormInput>({
    defaultValues: {
      name: currentState.name,
      birthYear: currentState.birthYear,
      birthMonth: currentState.birthMonth,
      birthDay: currentState.birthDay,
      jobtype: currentState.jobtype,
      interviewPlace: currentState.interviewPlace,
      motivation: currentState.motivation,
    }
  });

  const onSubmit = () => {
    const data = getValues();
    console.log('currentState!!!!!!');
    console.log(currentState);
    console.log(data);

    currentState.name = data['name'];
    currentState.birthYear = data['birthYear'];
    currentState.birthMonth = data['birthMonth'];
    currentState.birthDay = data['birthDay'];
    currentState.jobtype = data['jobtype'];
    currentState.interviewPlace = data['interviewPlace']
    currentState.motivation = data['motivation']

    const selectedPlace = currentState.interviewPlace;
    
    console.log('interviewVenues[selectedPlace]!!!!!!!');
    console.log(selectedPlace);
    console.log(schedulesForWorksheet[selectedPlace]);
    const targetSchedules = schedulesForWorksheet[selectedPlace];
    currentState.targetSchedules = targetSchedules;
    
    props.handleNext();
    setCurrentState({
      ...currentState,
    });
  };
  return (
    <>
      <div className="container p-0">
        <form onSubmit={handleSubmit(onSubmit)}>
          <div className="formBody">
            <div className="flex w-full flex-col
          justify-center rounded-lg p-5 my-5 bg-white">

              <div className="text-left">
                <div className="relative my-2">
                  <div className="flex mb-1">
                    <h2 className="text-[#27145d] text-sm mb-1 font-bold title-font">
                      お名前を入力してください。
                    </h2>
                    <div
                      className="ml-4 text-xs inline-flex 
                    items-center font-bold leading-sm uppercase px-3 pb-0 
                    bg-red-200 text-red-700 rounded-full">
                      必須
                    </div>
                  </div>
                  <p className="text-sm text-red-400 font-bold">{errors.name?.message}</p>
                  <input
                    type="text"
                    placeholder="山田 太郎"
                    className="text-xs w-full bg-gray-100 rounded  
                        focus:border-[#27145d] 
                        focus:ring-sky-200 outline-none
                        text-gray-700 py-2 px-3 leading-8 transition-colors duration-200 ease-in-out 
                        border-transparent"
                    {...register("name", {
                      'required': '※お名前を入力してください。',
                    })}
                  />
                </div>
              </div>

              <div className="text-left">
                <div className="relative my-4">
                  <div className="flex mb-1">
                    <h2 className="text-[#27145d] text-sm mb-1 font-bold title-font">
                      生年月日を入力してください。
                    </h2>
                    <div
                      className="ml-4 text-xs inline-flex 
                    items-center font-bold leading-sm uppercase px-3 py-1 
                    bg-red-200 text-red-700 rounded-full">
                      必須
                    </div>
                  </div>
                  <p className="text-sm text-red-400 font-bold">{errors.birthYear?.message}</p>
                  {/* <p className="text-sm text-red-400 font-bold">{errors.birthMonth?.message}</p>
                  <p className="text-sm text-red-400 font-bold">{errors.birthDay?.message}</p> */}
                  <div className="form_choices">
                    <div className="grid w-full gap-1 grid-cols-3">
                      <div className="flex">
                        {(() => {
                          const years = [];
                          years.push(<option key={0} value="">選択してください</option>);
                          for (let year = 1947; year <= 2004; year++) {
                            years.push(<option defaultValue={year} key={year}>{year}</option>);
                          }
                          return (
                            <select
                              {...register("birthYear", {
                                'required': '※年を選択してください。',
                              })}
                              className=" bg-gray-100 rounded  
                              focus:border-[#27145d] focus:ring-2 
                              focus:ring-sky-200 text-xs outline-none border-transparent
                              text-gray-700 py-2 px-2 leading-8 transition-colors duration-200 ease-in-out
                              w-full"
                              name="birthYear"
                            >
                              {years}
                            </select>);
                        })()}
                        <span className="p-2 text-sm">年</span>
                      </div>

                      <div className="flex">
                        {(() => {
                          const months = [];
                          months.push(<option key={0} value="">選択してください</option>);
                          for (let month = 1; month <= 12; month++) {
                            months.push(<option defaultValue={month} key={month}>{month}</option>);
                          }
                          return (
                            <select
                              {...register("birthMonth", {
                                'required': '※月を選択してください。',
                              })}
                              className=" bg-gray-100 rounded  
                              focus:border-[#27145d] focus:ring-2 
                              focus:ring-sky-200 text-xs outline-none border-transparent
                              text-gray-700 py-2 px-2 leading-8 transition-colors duration-200 ease-in-out
                              w-full"
                              name="birthMonth"
                            >
                              {months}
                            </select>);
                        })()}
                        <span className="p-2 text-sm">月</span>
                      </div>

                      <div className="flex">
                        {(() => {
                          const days = [];
                          days.push(<option key={0} value="">選択してください</option>);
                          for (let day = 1; day <= 31; day++) {
                            days.push(<option defaultValue={day} key={day}>{day}</option>);
                          }
                          return (
                            <select
                              {...register("birthDay", {
                                'required': '※日を選択してください。',
                              })}
                              className="bg-gray-100 rounded  
                              focus:border-[#27145d] focus:ring-2 
                              focus:ring-sky-200 text-xs outline-none border-transparent
                              text-gray-700 py-2 px-2 leading-8 transition-colors duration-200 ease-in-out
                              w-full"
                              name="birthDay"
                            >
                              {days}
                            </select>);
                        })()}
                        <span className="p-2 text-sm">日</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>


              <div className="text-left">
                <div className="relative my-4">
                  <div className="flex mb-1">
                    <h2 className="text-[#27145d] text-sm mb-1 font-bold title-font">
                      希望勤務地を選択してください。
                    </h2>
                    <div
                      className="ml-4 text-xs inline-flex 
                    items-center font-bold leading-sm uppercase px-3 py-1 
                    bg-red-200 text-red-700 rounded-full">
                      必須
                    </div>
                  </div>
                  <p className="text-sm text-red-400 font-bold">{errors.jobtype?.message}</p>
                  <ul className="grid w-full gap-1 grid-cols-2">
                    {jobtypeList.map((item, index) => {
                      return <div key={`div_key_${index}`}>
                        <li>
                          <input
                            type="radio"
                            id={item.key}
                            value={item.key}
                            className="hidden peer"
                            {...register("jobtype", {
                              'required': '※希望勤務地を選択してください。',
                            })}
                          />
                          <label htmlFor={item.key}
                            className="
                              inline-flex 
                              items-center 
                              text-xs 
                              justify-between w-full p-3 
                              text-gray-600 bg-gray-100 border border-gray-100 
                              rounded-lg cursor-pointer dark:hover:text-gray-600 
                              dark:peer-checked:text-sky-500 
                              peer-checked:border-sky-600 peer-checked:bg-white 
                              peer-checked:text-[#27145d] hover:text-gray-600 
                              hover:bg-gray-100 dark:text-gray-400 
                              dark:hover:bg-gray-200"
                          >
                            <div className="block">
                              <div className="w-full font-semibold">{item.text}</div>
                            </div>
                          </label>
                        </li>
                      </div>
                    })}
                  </ul>
                </div>
              </div>


              <div className="text-left">
                <div className="relative my-4">
                  <div className="flex mb-1">
                    <h2 className="text-[#27145d] text-sm mb-1 font-bold title-font">
                      希望面接地を選択してください。
                    </h2>
                    <div
                      className="ml-4 text-xs inline-flex 
                    items-center font-bold leading-sm uppercase px-3 py-1 
                    bg-red-200 text-red-700 rounded-full">
                      必須
                    </div>
                  </div>
                  <p className="text-sm text-red-400 font-bold">{errors.interviewPlace?.message}</p>
                  <div className="form_choices">
                    <div className="grid w-full gap-1 grid-cols-">
                      <div className="flex">
                        {(() => {
                          let selectPlaces = [];
                          selectPlaces.push(<option key={0} value="">選択してください</option>);
                          interviewVenues.map(
                            item => {
                              selectPlaces.push(
                                <option defaultValue={item} key={item}>{item}</option>
                              )
                            })
                          return (
                            <select
                              {...register("interviewPlace", {
                                'required': '※希望面接地を選択してください。',
                              })}
                              className=" bg-gray-100 rounded  
                              focus:border-[#27145d] focus:ring-2 
                              focus:ring-sky-200 text-xs outline-none border-transparent
                              text-gray-700 py-2 px-2 leading-8 transition-colors duration-200 ease-in-out
                              w-full"
                              name="interviewPlace"
                            >
                              {selectPlaces}
                            </select>);
                        })()}
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div className="text-left">
                <div className="relative my-4">
                  <div className="flex mb-1">
                    <h2 className="text-[#27145d] text-sm mb-1 font-bold title-font">
                      志望動機を入力してください。
                    </h2>
                    <div
                      className="ml-4 text-xs inline-flex 
                    items-center font-bold leading-sm uppercase px-3 py-1 
                    bg-red-200 text-red-700 rounded-full">
                      必須
                    </div>
                  </div>
                  <p className="text-sm text-red-400 font-bold">{errors.motivation?.message}</p>
                  <div className="form_choices">
                    <div className="grid w-full gap-1 grid-cols-">
                      <div className="flex">
                        <textarea
                          {...register("motivation", {
                            'required': '※志望動機を入力してください。',
                            'maxLength': { value: 100, message: '100文字以内で入力してください。' },
                          })}
                          className=" bg-gray-100 rounded  
                          focus:border-[#27145d] focus:ring-2 
                          focus:ring-sky-200 text-xs outline-none border-transparent
                          text-gray-700 py-2 px-2 leading-8 transition-colors duration-200 ease-in-out
                          w-full"
                          name="motivation"
                          placeholder="志望動機（100文字以内）"
                        >
                        </textarea>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div >
          <div className="mt-5 mb-10 text-center">
            <div className="col-12">
              <button type="submit"
                className="text-white bg-[#554a7d] hover:bg-[#27145d] focus:ring-4 
                focus:outline-none focus:ring-blue-300 
                font-bold rounded-lg text-lg 
                px-5 py-3 text-center inline-flex items-center
                border-b-4 border-[#27145d]" >
                次へ
                <svg aria-hidden="true" className="w-5 h-5 ml-2 -mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                  <path fillRule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clipRule="evenodd">
                  </path>
                </svg>
              </button>
            </div>
          </div>
        </form >
      </div >
    </>
  )
}

export default WorksheetFirst