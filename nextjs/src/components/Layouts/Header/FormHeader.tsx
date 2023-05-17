import React, { useState } from "react";

interface FormHeaderProps {
}

const FormHeader = (props: FormHeaderProps) => {
  return (
    <>
      <div className="bg-gray-100 p-3">
        <div className="w-100">
          {/* <img
            src={logoUrl}
            className="logo w-50"
          /> */}
        </div>
        <div>
          <p className="w-100 text-center text-xs">
            ご応募いただきましてありがとうございます。<br />
            当アンケートにて選考の内容を確認しております。<br />
            下記質問の回答完了後、<br />
            弊社採用担当よりご連絡させていただきます。
          </p>
        </div>
      </div>
    </>
  );
}
export default FormHeader
