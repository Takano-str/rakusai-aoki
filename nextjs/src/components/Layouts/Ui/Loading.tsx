import React from "react";

// export default function Loading(props: { disp: boolean }) {
export default function Loading() {
  // const disp = props.disp;
  return (
    <div id="loader" className=""
    style={{
        position: 'fixed',
        top: '0',
        left: '0',
        width: '100vw',
        height: '100vh',
        backgroundColor: "#000",
        opacity: 0.5,
        zIndex: 9000,
      }}
    >
      <div id="animation"
      style={{
        margin: 0,
        position: 'absolute',
        top: '50%',
        left: '50%',
        transform: 'translate(-50%, -50%)',
        zIndex: 9999,
      }}
      >
        <img
          src="/img/loading.gif"
          alt="ローディング画像"
          style={{
            width: '50px',
            height: '50px',
          }}
        />
        <p className="">送信中...</p>
      </div>
    </div>
  );
}