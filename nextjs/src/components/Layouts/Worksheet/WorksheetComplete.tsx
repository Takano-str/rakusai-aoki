import React, { createContext, useState, useEffect, useContext } from "react";
import { useForm, Controller, SubmitHandler } from "react-hook-form";
import { Button, MenuItem, styled } from "@material-ui/core";
// import { UserInputData } from "./KaigoworkSteps";
// import Loading from "../../../ui/Loading";


const WorksheetComplete = () => {
  const [loading, setLoading] = useState(false);
  // const [checkedItems, setCheckedItems] = useState(selectedPref)

  return (
    <>
      {/* {loading ? <Loading disp={true} /> : <br />} */}
      <div className="container p-0 h-screen">
        <form>
          <div className="formBody">
            <div className="flex w-full flex-col
          justify-center rounded-lg p-5 my-5 bg-white">
              <div className="text-center">
                <div className="relative my-2">
                  {/* <div className="flex mb-1"> */}
                  <div className="text-center">
                    <h2 className="text-[#27145d] text-lg mb-1 font-bold title-font text-center">
                      回答完了しました。
                    </h2>
                  </div>
                </div>
              </div>
            </div>
            <div className="card p-5">
              <p className="text-[#27145d] text-base mb-1 font-bold title-font text-center">
                ご回答いただき、ありがとうございます！</p>
              <ul className="card__inputField">
                <li className="text-[#27145d] text-base mb-1 font-bold title-font text-center">
                  担当者より連絡させていただきます。</li>
              </ul>
            </div>
          </div >
        </form >
      </div >
    </>
  )
}
export default WorksheetComplete
