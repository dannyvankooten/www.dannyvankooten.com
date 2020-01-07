---
layout: post
title: Stock market investments return calculator
date: '2017-08-11 13:56:00'
tags:
- investing
---

During the past few months I have been reading up on personal finance, including investing and investment strategies. While doing so it is pretty much impossible to miss out on Jack Bogle. Bogle is the founder of Vanguard, the creator of the first Exchange Traded Fund. There's even the aptly named [Bogleheads](https://www.bogleheads.org/) community.

Basically, their philosophy comes down to the following. **No one can beat or time the market consistently, therefore it is best to buy low-cost diversified index funds, tracking markets as closely as possible.** If you believe the market you're investing in is efficient, that is.

Intrigued, I started digging into historical data for various stock market indices to see what my return on investment would have been if I entered the market at various points in time, for example just before a market crash or correction. By the way, meet [Bob, world's worst market timer](http://awealthofcommonsense.com/2014/02/worlds-worst-market-timer/).

There were various calculators available for the S&P 500 index already, but I could not really find one for major European market indices like the AEX, DAX or CAC40. Since [Yahoo Finance](https://finance.yahoo.com/) has historical data for all of these indices freely available, I decided to hack together a calculator myself (see below).

After entering your start and end date, you can see how your annualized rate of return would have changed over the selected time period by doing nothing else than simply buying and holding on to your investment.


<style type="text/css" scoped>
  label { display: block; font-weight: bold; }
  .input-group{ margin: 20px 0; }
  .well {
    background: #eef5fa;
    border: 1px solid #dae9f4;
    padding: 0 20px;
    box-shadow: 0 0 1px 1px #eee;
  }
  .small-padding { padding-top: 20px; padding-bottom: 20px; }
  .smaller-text{
    font-size: 90%;
  }

  select {
    padding: 4px 6px;
  }

  button{
    background: #09f;
    color: white;
    border: 1px solid #458;
    cursor: pointer;
    padding: 4px 12px;
  }

  button:hover {
    background: #458;
  }

  #chart { width: 100%; min-height: 400px; }
</style>

<form id="calculate-form" class="well">

  <div class="input-group">
    <label>Market index</label>
    <select id="market-select">
      <option value="SP500">S&amp;P 500</option>	
	  <option value="AEX">AEX</option>
      <option value="CAC40">CAC 40</option>
	  <option value="DAX">DAX</option>
	  <option value="N100">Euronext 100</option>
      <option value="N225">Nikkei 225</option>
	  <option value="SSEC">SSE Composite</option>
    </select>
  </div>

  <div class="input-group">
    <label>Start date</label>
    <select id="start-month-select" class="month-select">
      <option disabled>Month</option>
      <option value="1">Jan</option>
      <option value="2">Feb</option>
      <option value="3">Mar</option>
      <option value="4">Apr</option>
      <option value="5">May</option>
      <option value="6">Jun</option>
      <option value="7">Jul</option>
      <option value="8">Aug</option>
      <option value="9">Sep</option>
      <option value="10">Oct</option>
      <option value="11">Nov</option>
      <option value="12">Dec</option>
    </select>
    <select id="start-year-select" class="year-select">
        <option disabled>Year</option>
    </select>
  </div>

  <div class="input-group">
    <label>End date</label>
    <select id="end-month-select" class="month-select">
      <option disabled>Month</option>
      <option value="1">Jan</option>
      <option value="2">Feb</option>
      <option value="3">Mar</option>
      <option value="4">Apr</option>
      <option value="5">May</option>
      <option value="6">Jun</option>
      <option value="7">Jul</option>
      <option value="8">Aug</option>
      <option value="9">Sep</option>
      <option value="10">Oct</option>
      <option value="11">Nov</option>
      <option value="12">Dec</option>
    </select>
    <select id="end-year-select" class="year-select">
      <option disabled>Year</option>
    </select>
  </div>

  <div class="input-group">
    <button>Calculate</button>
  </div>
</form>

<div class="well small-padding" id="results" style="display: none; border-top: 0;">
  <div>
    <strong>Total return: </strong><span id="total-return"></span> <br />
    <em class="smaller-text">The total price return of the selected index.</em>
  </div>
  <div style="margin-top: 20px;">
    <strong>Annualized return: </strong><span id="annualized-return"></span> <br />
    <em class="smaller-text">The total price return of the selected index, annualized. This number basically gives your ‘return per year’ if your time period was compressed or expanded to a 12 month timeframe.</em>
  </div>

  <div id="chart" style="margin-top: 20px;"></div>
</div>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script src="https://code.jquery.com/jquery-3.4.1.min.js"
        integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
        crossorigin="anonymous"></script>
<script>
  function onMarketSelectChange(e) {
    var market = this.value;
    fetchMarketData(market);
  }

  function fetchMarketData(market) {
    jQuery.ajax({
       type: "GET",
       url: "/data/" + market + ".csv",
       dataType: "text",
       success: processMarketData,
    });
  }

  function processMarketData(data) {
    // reset marketData
    marketData = {};

    // split by newline
    data = data.split("\n");

    // strip header row
    data = data.slice(1);

    // Date,Open,High,Low,Close,Adj Close,Volume
    // 2017-06-30,509.540009,530.659973,506.269989,525.440002,525.440002,1643519600
    for(var i=0; i<data.length; i++) {
      var row = data[i].split(',');
      var close = row[5];
      var dateparts = row[0].split('-');
      var year = dateparts[0];
      var month = parseInt(dateparts[1]);
      if( year == "" ) { continue; }
      if( typeof(marketData[year]) === "undefined" ) {
        marketData[year] = {};
      }

      marketData[year][month] = parseFloat(close);
    }

    // populate date select elements
    $(".year-select option:gt(0)").remove();
    $.each(marketData, function(year, v) {
      // TODO: Add years with partial data.
      if(typeof(marketData[year][1]) !== "undefined") {
        $('.year-select').append("<option>" + year + "</option>")
      }
    });

    $("#end-year-select option:last").prop('selected', true);
  }

  function roundP(number, precision) {
    var factor = Math.pow(10, precision);
    var tempNumber = number * factor;
    var roundedTempNumber = Math.round(tempNumber);
    return roundedTempNumber / factor;
  }


  function onCalculateFormSubmit(e) {
    e.preventDefault();

    if( endYearSelect.value < startYearSelect.value || (  endYearSelect.value  == startYearSelect.value && endMonthSelect.value <= startMonthSelect.value )) {
      alert('End date should come after start date!');
      return;
    }

    if( typeof(marketData[endYearSelect.value][endMonthSelect.value]) === "undefined" ) {
      alert('Sorry, no data for that ending month yet. Please pick an earlier ending date.');
      return;
    }

    // (end / start)^(1/years) - 1
    var end = marketData[endYearSelect.value][endMonthSelect.value];
    var start = marketData[startYearSelect.value][startMonthSelect.value];
    var months = 0;
    for(var cYear = startYearSelect.value; cYear <= endYearSelect.value; cYear++) {
      if(cYear == startYearSelect.value) {
        months += ( 13 - parseInt(startMonthSelect.value) );
      } else if(cYear == endYearSelect.value) {
        months += parseInt(endMonthSelect.value) - 1;
      } else {
        months += 12;
      }
    }
    var years = parseFloat( months / 12 );
    var totalReturn = ( ( end / start ) - 1.00 ) * 100.00;
    var annualizedReturn = ( Math.pow(end / start, ( 1.00 / years )) - 1.00 ) * 100.00; // TODO: Fix this.

    resultsEl.style.display = '';
    totalReturnEl.innerHTML = roundP(totalReturn, 2) + "%";
    annualizedReturnEl.innerHTML = roundP(annualizedReturn, 2) + "%";

    (function() {
      var chartData = new google.visualization.DataTable();
      chartData.addColumn('string', 'Date' );
      chartData.addColumn('number', 'Return');

      var startValue = marketData[startYearSelect.value][startMonthSelect.value];
      var totalMonths = 1;

      for( cYear = startYearSelect.value; cYear <= endYearSelect.value; cYear++ ) {
        var cMonth = 1;

        for( var cMonth = 1; cMonth <= 12; cMonth++ ) {
          // skip first (selected) month of first year
          if(cYear == startYearSelect.value && cMonth <= startMonthSelect.value) {
            continue;
          }

          // break when done
          if(cYear == endYearSelect.value && cMonth > endMonthSelect.value) {
            break;
          }

          var endValue = marketData[cYear][cMonth];
          var annualizedReturn = ( Math.pow(endValue / startValue, ( 1.00 / ( parseFloat(totalMonths / 12 ) ) ) ) - 1.00 ) * 100.00;

          var label = cYear + "-" + ("0" + cMonth).slice(-2);
          var value = roundP(annualizedReturn, 2);
          chartData.addRows([
            [ label, value ]
          ]);

          totalMonths++;
        }
      }

      chart.draw(chartData, chartOptions);
    })();

  }

  var marketData ={};
  var calculateForm = document.getElementById('calculate-form');
  var marketSelect = document.getElementById('market-select');
  var startMonthSelect = document.getElementById('start-month-select');
  var startYearSelect = document.getElementById('start-year-select');
  var endMonthSelect = document.getElementById('end-month-select');
  var endYearSelect = document.getElementById('end-year-select');
  var yearSelects = document.querySelectorAll('.year-select');
  var resultsEl = document.getElementById('results');
  var totalReturnEl = document.getElementById('total-return');
  var annualizedReturnEl = document.getElementById('annualized-return');
  var chart;
  var chartOptions = {
    vAxis: {
      title: 'Annualized return %',
    },
    hAxis: {
      title: 'Date'
    },
    width: 678,
    height: 350
  };

  marketSelect.addEventListener('change', onMarketSelectChange);
  calculateForm.addEventListener('submit', onCalculateFormSubmit);
  onMarketSelectChange.call(marketSelect);

  google.charts.load('current', {'packages':[ 'corechart', 'line']});
  google.charts.setOnLoadCallback(function() {
    chart = new google.visualization.LineChart(document.getElementById('chart'));
  });

</script>



### Takeways from the stock market ROI calculator

I think that the tool nicely shows that as your holding period increases:

- there is less volatility
- returns become more predictable
- for long enough time periods, negative returns are almost non-existing

Of course, past performance is no guarantee for future results.

That said, past performance can be used to see if your expectations are reasonable. If you expect an annualized 10% return over a 20 year investment in a fund tracking the AEX, it helps to know that that has never happened before.
