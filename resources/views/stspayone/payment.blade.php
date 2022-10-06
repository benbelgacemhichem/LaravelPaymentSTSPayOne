
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>TSD Payment</title>

    <style>
        :root {
            --yellow: #feb60a;
            --red: #ff0062;
            --blue: #00dbf9;
            --violet: #da00f7;
        }

        body {
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #1a1940;
            background-image: linear-gradient(180deg,
                    rgba(0, 0, 0, 0.15) 0%,
                    rgba(0, 153, 212, 0) calc(15% + 100px),
                    rgba(0, 99, 138, 0) calc(85% + 100px),
                    rgba(0, 0, 0, 0.15) 100%);
        }

        div.container {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        div>div {
            width: 3vw;
            height: 3vw;
            border-radius: 100%;
            margin: 2vw;
            background-image: linear-gradient(145deg,
                    rgba(255, 255, 255, 0.5) 0%,
                    rgba(0, 0, 0, 0) 100%);
            animation: bounce 1.5s 0.5s linear infinite;
        }

        .yellow {
            background-color: var(--yellow);
        }

        .red {
            background-color: var(--red);
            animation-delay: 0.1s;
        }

        .blue {
            background-color: var(--blue);
            animation-delay: 0.2s;
        }

        .violet {
            background-color: var(--violet);
            animation-delay: 0.3s;
        }

        @keyframes bounce {

            0%,
            50%,
            100% {
                transform: scale(1);
                filter: blur(0px);
            }

            25% {
                transform: scale(0.6);
                filter: blur(3px);
            }

            75% {
                filter: blur(3px);
                transform: scale(1.4);
            }
        }
    </style>
</head>

<body onload="javascript:document.redirectForm.submit();">

    <form action="{{$data['PG_REDIRECT_URL']}}" method="POST" name="redirectForm">
        <input type="hidden" name="Action" value="{{$data['Action']}}" />
        <input type="hidden" name="Amount" value="{{$data['Amount']}}" />
        <input type="hidden" name="BankID" value="{{$data['BankID']}}" />
        <input type="hidden" name="CurrencyCode" value="{{$data['CurrencyCode']}}" />
        <input type="hidden" name="ExtraFields_f14" value="{{$data['ExtraFields_f14']}}" />
        <input type="hidden" name="Lang" value="{{$data['Lang']}}" />
        <input type="hidden" name="MerchantID" value="{{$data['MerchantID']}}" />
        <input type="hidden" name="MerchantModuleSessionID" value="{{$data['MerchantModuleSessionID']}}" />
        <input type="hidden" name="PaymentDescription" value="{{$data['PaymentDescription']}}" />
        <input type="hidden" name="PUN" value="{{$data['PUN']}}" />
        <input type="hidden" name="Quantity" value="{{$data['Quantity']}}" />
        <input type="hidden" name="SecureHash" value="{{$data['SecureHash']}}" />
        <input type="hidden" name="TransactionRequestDate" value="{{$data['TransactionRequestDate']}}" />
    </form>

    <div class="container">
        <div class="yellow"></div>
        <div class="red"></div>
        <div class="blue"></div>
        <div class="violet"></div>
    </div>
</body>

</html>
