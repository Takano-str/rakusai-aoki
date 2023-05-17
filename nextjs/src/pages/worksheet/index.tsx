import type { NextPage } from 'next'
import React, {
  createContext, useState,
  Dispatch, SetStateAction
} from "react";
import { Grid, makeStyles, styled } from '@material-ui/core'
import Step from '@material-ui/core/Step';
import FormHeader from '@/components/Layouts/Header/FormHeader';
import WorksheetFirst from '@/components/Layouts/Worksheet/WorksheetFirst';
import WorksheetSecond from '@/components/Layouts/Worksheet/WorksheetSecond';
import WorksheetComplete from '@/components/Layouts/Worksheet/WorksheetComplete';
import { UserData, Schedule, WorksheetProps, WorksheetData } from '@/types/worksheet'
// import Stepper from '@material-ui/core/Stepper';
// import StepLabel from '@material-ui/core/StepLabel';
// import { ObjectEncodingOptions } from "fs";
// import { resourceUsage } from 'process';

const getSteps = () => {
  return [
    'Web選考',
    '日時選択',
    // '完了',
  ];
}

const CustomGrid = styled(Grid)({
  // backgroundColor: '#EBF8F2'
  // backgroundColor: '#d8edfb'
  backgroundColor: '#E9F3CD'
})
const CustomStep = styled(Step)({
  '& .MuiStepIcon-root.MuiStepIcon-active': {
    // color: '#3ab27a',
    color: '#3765a5',
  }
})

export const UserInputData = createContext({} as {
  currentState: UserData
  setCurrentState: Dispatch<SetStateAction<UserData>>
});

const Worksheet: NextPage = (props: WorksheetProps) => {
  const worksheetData = props.data;
  const isAnswered = worksheetData.isAnswered;
  const consumerId = worksheetData.consumerId;
  const schedules = worksheetData.schedules;
  const interviewVenues = worksheetData.interviewVenues;
  const schedulesForWorksheet = worksheetData.schedulesForWorksheet;
  const anotherDateArray = worksheetData.anotherDateArray;
  // console.log(interviewVenues);
  // console.log(schedulesForWorksheet);

  const assetPath = '';
  const [loading, setLoading] = useState(false);
  // logoUrl = assetPath + "/img/kaigo-work-logo.png";
  // const [currentState, setCurrentState] = useState({})


  // 選択した職種によって次ページで表示させるパターンを変更する
  const [pattern, setPattern] = useState('');
  const [activeStep, setActiveStep] = React.useState(0);
  const steps = getSteps();
  const handleNext = () => {
    setActiveStep((prevActiveStep) => prevActiveStep + 1);
  };
  const handleBack = () => {
    setActiveStep((prevActiveStep) => prevActiveStep - 1);
  };
  const handleReset = () => {
    setActiveStep(0);
  };
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

  const getStepContent = (stepIndex: number, handleNext: any, handleBack: any) => {
    if (isAnswered) {
      return <WorksheetComplete
      />;
    }
    switch (stepIndex) {
      case 0:
        return <>
          <WorksheetFirst
            schedulesForWorksheet={schedulesForWorksheet}
            interviewVenues={interviewVenues}
            assetPath={assetPath}
            setPattern={setPattern}
            handleNext={handleNext}
          />
        </>
      case 1:
        return <>
          <WorksheetSecond
            consumerId={consumerId}
            schedules={schedules}
            // schedulesForWorksheet={schedulesForWorksheet}
            anotherDateArray={anotherDateArray}
            setPattern={setPattern}
            handleNext={handleNext}
            handleBack={handleBack}
          />
        </>

      case 2:
        return <WorksheetComplete
        />;
      // return <WorksheetStepsConfirm
      //   handleNext={handleNext}
      //   handleBack={handleBack}
      // />;
      default:
        return 'Unknown stepIndex';
    }
  }

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

              {/* ステップ表示（なくてもいい） */}
              {/* <Stepper activeStep={activeStep} alternativeLabel>
                {steps.map((label) => (
                  <CustomStep key={label}>
                    <StepLabel>
                      {label}
                    </StepLabel>
                  </CustomStep>
                ))}
              </Stepper> */}

            </div>
            {/* <Stepper activeStep={activeStep} alternativeLabel>
              {steps.map((label) => (
                <Step key={label}>
                  <StepLabel>{label}</StepLabel>
                  <StepLabel></StepLabel>
                </Step>
              ))}
            </Stepper> */}
          </Grid>

          <Grid item xs={12} >
            <FormHeader />
          </Grid>

          <CustomGrid item lg={3} sm={3} xs={1} />
          {/* <Grid item lg={8} sm={8} spacing={10}> */}
          <CustomGrid item lg={6} sm={6} xs={12}>
            {/* 一旦コメントアウトしておく */}
            {/* <Stepper activeStep={activeStep} alternativeLabel>
              {steps.map((label) => (
                <Step key={label}>
                  <StepLabel>{label}</StepLabel>
                </Step>
              ))}
            </Stepper> */}

            <UserInputData.Provider value={{ currentState, setCurrentState }}>
              {getStepContent(activeStep, handleNext, handleBack)}
            </UserInputData.Provider>
          </CustomGrid>

          <CustomGrid item lg={3} sm={3} xs={1} />
        </Grid>
      </div>
    </>
  )
}

// export async function getStaticProps(context) {
//   const csid = context.query.csid;
//   const apiRequestUrl = `http://localhost:10082/api/worksheet?csid=${csid}`
//   const res = await fetch(apiRequestUrl)
//   const posts = await res.json()
//   return {
//     props: {
//       posts,
//     },
//     revalidate: 10, // In seconds
//   }
// }
// export async function getStaticPaths(context) {
//   const csid = context.query.csid;
//   const apiRequestUrl = `http://localhost:10082/api/worksheet?csid=${csid}`
//   const res = await fetch(apiRequestUrl)
//   const posts = await res.json()
//   const paths = posts.map((post) => ({
//     params: { id: post.id },
//   }))
//   return { paths, fallback: 'blocking' }
// }

export const getServerSideProps = async (context) => {
  const csid = context.query.csid;
  const cskey = context.query.cskey;
  const apiRequestUrl = process.env.NEXT_PUBLIC_BACKEND_URL_API
    + `/worksheet?csid=${csid}`
    + `&cskey=${cskey}`
  const response = await fetch(encodeURI(apiRequestUrl));
  return {
    props: {
      data: await response.json()
    }
  }
};

export default Worksheet;
