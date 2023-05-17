import Head from 'next/head'
import Link from 'next/link'
import { useAuth } from '@/hooks/auth'
// import { lookupService } from "dns";

export default function Home(props) {
  console.log(props)
  const ipAddress: string = props.data;
  // const { user } = useAuth({ middleware: 'guest' })
  if (!ipAddress.match(/172.21.0.1|159.28.73.98/g)) {
    return <></>;
  }
  return <></>;
}

// const getHostName = (ipAddress: string): Promise<string> => {
//   return new Promise(function (resolve, reject) {
//     lookupService(ipAddress, 22, function (error, hostname, service) {
//       if (error) {
//         return reject(error);
//       }
//       resolve(hostname);
//     });
//   });
// };

export const getServerSideProps = async (context) => {
  const { req } = context;
  const ipAddress = req.headers["x-forwarded-for"]
    ? String(req.headers["x-forwarded-for"]).split(',')[0]
    : req.connection.remoteAddress
      ? req.connection.remoteAddress
      : "";
  // const hostName = await getHostName(ipAddress);
  return {
    props: {
      data: await ipAddress
    }
  }
}
