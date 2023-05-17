import React from 'react';
import Document, { Html, Head, Main, NextScript } from 'next/document'
import { ServerStyleSheets } from '@material-ui/core/styles';

class MyDocument extends Document {
    static async getInitialProps(ctx) {
        const sheets = new ServerStyleSheets();
        const originalRenderPage = ctx.renderPage;
        ctx.renderPage = () =>
        originalRenderPage({
            enhanceApp: (App) => (props) => sheets.collect(<App {...props} />),
        });

        const initialProps = await Document.getInitialProps(ctx)
        return { 
            ...initialProps,
            styles: [...React.Children.toArray(initialProps.styles), sheets.getStyleElement()],
        }
    }

    render() {
        return (
            <Html>
                <Head>
                    <link
                        href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap"
                        rel="stylesheet"
                    />
                </Head>
                <body className="antialiased">
                    <Main />
                    <NextScript />
                </body>
            </Html>
        )
    }
}

export default MyDocument
