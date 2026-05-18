<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Invoice</title>
    {{-- <link rel="stylesheet" href="/style.css" media="all" /> --}}
    <style>
        .clearfix:after {
            content: "";
            display: table;
            clear: both;
        }

        a {
            color: #5D6975;
            text-decoration: underline;
        }

        body {
            margin: auto !important;
            position: relative;
            /* width: 21cm; */
            height: 29.7cm;
            /* margin: 0 auto;  */
            color: #001028;
            background: #FFFFFF;
            font-family: Arial, sans-serif;
            font-size: 12px;
            font-family: Arial;
        }

        header {
            padding: 10px 0;
            margin-bottom: 30px;
        }

        #logo {
            text-align: center;
            margin-bottom: 10px;
        }

        #logo img {
            width: 90px;
        }

        h1 {
            border-top: 1px solid #5D6975;
            border-bottom: 1px solid #5D6975;
            color: #5D6975;
            font-size: 2.4em;
            line-height: 1.4em;
            font-weight: normal;
            text-align: center;
            margin: 0 0 20px 0;
            background: url(dimension.png);
        }

        #project {
            float: left;
        }

        #project span {
            color: #5D6975;
            text-align: right;
            width: 52px;
            margin-right: 10px;
            display: inline-block;
            font-size: 0.8em;
        }

        #company {
            float: right;
            text-align: right;
        }

        #project div,
        #company div {
            white-space: nowrap;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
            margin-bottom: 20px;
        }

        table tr:nth-child(2n-1) td {
            background: #F5F5F5;
        }

        table th,
        table td {
            text-align: center;
        }

        table th {
            padding: 5px 20px;
            color: #5D6975;
            border-bottom: 1px solid #C1CED9;
            white-space: nowrap;
            font-weight: normal;
        }

        table .service,
        table .desc {
            text-align: left;
        }

        table td {
            padding: 20px;
            text-align: center;
        }

        table td.service,
        table td.desc {
            vertical-align: top;
        }

        table td.unit,
        table td.qty,
        table td.total {
            font-size: 1.2em;
        }

        table td.grand {
            border-top: 1px solid #5D6975;
            ;
        }

        #notices .notice {
            color: #5D6975;
            font-size: 1.2em;
        }

        footer {
            color: #5D6975;
            width: 100%;
            height: 30px;
            position: absolute;
            bottom: 0;
            border-top: 1px solid #C1CED9;
            padding: 8px 0;
            text-align: center;
        }

        .text-alignment {
            text-align: right !important;
        }
    </style>
  </head>
  <body>
      <header class="clearfix">

        <div class="row d-flex justify-content-between">

          <div class="col" id="logo">

            <img src="{{ public_path() }}/images/avatar/DMDLOGO.jpg">


          </div>

        </div>


        <h1>Receipt {{$appointment->serial_number}}</h1>
        <div id="company" class="clearfix">
            <div style="font-weight: bold;
            font-size: 18px;">Clinic Details</div>
          <div>{{auth()->user()->business_name}}</div>
          <div>{{auth()->user()->address}}<br /> {{auth()->user()->postal_code}}, {{auth()->user()->country}}</div>
          <a href="tel:+{{auth()->user()->phone}}">{{auth()->user()->phone}}</a>
          <div><a href="mailto:{{auth()->user()->business_email}}">{{auth()->user()->business_email}}</a></div>
        </div>
        <div id="">
            <div style="font-weight: bold;
            font-size: 18px;">Patient Details</div>
          <div><span>Name -</span>{{$appointment->Customer->name}}</div>
          <div><span>Phone -</span>{{$appointment->phone}}</div>
          <div><span>DATE -</span>{{$appointment->created_at->toFormattedDateString()}}</div>
          <div><span>Recipt No -</span> <span style="font-weight: bold;">{{$appointment->serial_series}}</span></div>

          <div><span>EMAIL</span> <a href="mailto:{{$appointment->Customer->email}}">{{$appointment->Customer->email}}</a></div>
        </div>

      </header>
      <main>
        <table>
          <thead>
            <tr>
              <th class="service">Service Name</th>
              <th>PRICE</th>
              <th>Service Price</th>
              <th>Final Price</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($appointment->appointmentService as $sale )
            <tr>
              <td class="service">{{$sale->name}}</td>
              <td class="unit">{{auth()->user()->currency}}{{$sale->price}}</td>
              <td class="unit">{{auth()->user()->currency}}{{$sale->discounted_price}}</td>
              <td class="total">{{auth()->user()->currency}}{{$sale->discounted_price}}</td>
            </tr>
            @endforeach

            <tr>
              <td colspan="3" class="text-alignment">SUBTOTAL</td>
              <td class="total ">{{auth()->user()->currency}}{{$appointment->subtotal_discounted_price}}</td>
            </tr>
            <tr>
              <td colspan="3" class="text-alignment">Discount</td>
              <td class="total">{{auth()->user()->currency}}{{$appointment->discount??0}}</td>
            </tr>
            <tr>
              <td colspan="3" class="grand total text-alignment" >GRAND TOTAL</td>
              <td class="grand total">{{auth()->user()->currency}}{{$appointment->subtotal_discounted_price_after_discount}}</td>
            </tr>
          </tbody>
        </table>
        <div id="notices">
          <div>{{auth()->user()->custom_note_heading}}</div>
          <div class="notice">{{auth()->user()->custom_note}}</div>
        </div>
      </main>
      <footer>
        Invoice was created on a computer and is valid without the signature and seal.
      </footer>
  </body>
</html>

