export type Schedule = {
  id: string
  start_date: string
  end_date: string
  store_id: string
};

export type DecideSchedule = {
  start_date: string
  end_date: string
  interview_venue: string
  store_id: number
}

export interface StatusData {
  ats_id: number
  // decideSchedule : DecideSchedule
  start_date: string
  end_date: string
  interview_venue: string
  store_id: number
  google_meet_urls: string[]
  venue_address: string
}

export interface WorksheetProps {
  data: WorksheetData
}

export interface StatusProps {
  data: StatusData
}

export interface WorksheetAnswerData {
  name: string
  jobtype: string
  birthDay: string
  birthYear: string
  birthMonth: string
  motivation: string
  interviewPlace: string
  isAnotherAdjust: string[]
  StepInterviewSchedule: string[]
}

export interface WorksheetStoreProps {
  consumerId: string
  name: string
  ats_id: string
  tel: string
  mail: string
  adjustSchedules: string[]
  worksheetAnswer: WorksheetAnswerData
}

export interface WorksheetData {
  isAnswered: boolean
  consumerId: string
  apiRequestUrl: string
  schedules: Schedule[]
  schedulesForWorksheet: SchedulesForWorksheet[]
  interviewVenues: string[]
  anotherDateArray: string[]
  privacy: string
  blackListFlag: boolean
  prefCityJson: Array<object>
}

export interface SchedulesForWorksheet {
  // venue: {
  //   string:Schedule[]
  // }
}

/** @todo anyは直す **/
export interface UserData {
  name: string
  birthYear: string;
  birthMonth: string;
  birthDay: string;
  jobtype: string,
  interviewPlace: string,
  motivation: string,

  first_choice_select: string,
  second_choice_select: string,
  third_choice_select: string,

  StepInterviewSchedule: any
  StepInterviewAnotherSchedule: any

  isAnotherAdjust: boolean
  targetSchedules: Schedule[]
}

export interface StoreAnswerData {
  consumerId: string
  scheduleType: string
  interviewDate: string
}
