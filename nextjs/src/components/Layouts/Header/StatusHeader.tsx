import React, { useState } from "react";

interface StatusHeaderProps {
}

const StatusHeader = (props: StatusHeaderProps) => {
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
            面接日程のご案内です。<br />
            日程変更、キャンセルは下部お問合せ先からご連絡ください。<br />
            {/* 下記質問の回答完了後、<br />
            弊社採用担当よりご連絡させていただきます。 */}
          </p>
        </div>
      </div>
    </>
  );
}
export default StatusHeader
