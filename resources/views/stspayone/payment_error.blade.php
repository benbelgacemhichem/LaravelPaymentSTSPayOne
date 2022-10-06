<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        html,
        body {
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        body {
            overflow: hidden;
        }

        .background {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            background-size: cover;
            background-position: center;
            background: #ffffff;
        }

        .payment_successImg {
            width: 30%;
        }

        .payment-succes-title {
            font-weight: 700;
            letter-spacing: -0.5px;
            margin-bottom: 25px;
        }

        .payment-content {
            color: #808080;
            width: 30%;
            margin: auto;
        }

        .button-sliding-icon {
            background-color: #eee;
            color: #606060;
            margin-top: 15px;
            font-weight: 600;
            font-size: 14px;
            box-shadow: none;
            padding-top: 10px;
            padding-bottom: 10px;
        }

        .button-sliding-icon:hover i {
            max-width: 20px;
            opacity: 1;
            transform: translateX(0);
        }

        .button-sliding-icon:hover i {
            max-width: 20px;
            opacity: 1;
            transform: translateX(0);
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="text-center">
                <img src="{{ asset('/payment/payment-failure.gif') }}" class="payment_successImg">
                <h1 class="payment-succes-title">Payment Failed!</h1>
                <p class="payment-content">Oops payment failed, something went wrong please try again.</p>
                <p class="payment-content">{{ $error }}</p>
                <br>
                @if(Request::get('status'))
                <div style="text-align: center;">
                    <p> Transaction Number : {{ Request::get('pun') }} - Amount: {{ Request::get('amount') }} QAR - Status: {{ Request::get('status') }}</p>
                    <p>Date: {{ Request::get('date') }}</p>
                </div>
                @endif
                @if(Request::get('message'))
                <div style="text-align: center;">
                    <p style="color: red">Notice: {{ Request::get('message') }}</p>
                </div>
                @endif
                <br>
                <a href="https://tsdoha.com/" type="button" class="btn button-sliding-icon">
                    Go Back
                    <span><i class="fa fa-long-arrow-right" aria-hidden="true"></i></span>
                </a>
            </div>
        </div>
    </div>
</body>

</html>