---
layout: page
title: Stock Market Return Calculator
permalink: /stock-market-return-calculator/
---

Below is an investment return calculator for some (random) indices. Historical data is taken from [Yahoo Finance](https://finance.yahoo.com/).

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
</style>

<form id="calculate-form" class="well">

  <div class="input-group">
    <label>Market index</label>
    <select id="market-select">
      <option>AEX</option>
      <option value="BFX">BEL 20</option>
      <option value="CAC40">CAC 40</option>
      <option>DAX</option>
      <option value="STOXX50">EURO STOXX 50</option>
      <option>IBEX</option>
      <option value="N225">Nikkei 225</option>
      <option value="SP500">S&P 500</option>
    </select>
  </div>

  <div class="input-group">
    <label>Start date</label>
    <select id="start-month-select" class="month-select">
      <option disabled>Month</option>
      <option value="01">Jan</option>
      <option value="02">Feb</option>
      <option value="03">Mar</option>
      <option value="04">Apr</option>
      <option value="05">May</option>
      <option value="06">Jun</option>
      <option value="07">Jul</option>
      <option value="08">Aug</option>
      <option value="09">Sep</option>
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
      <option value="01">Jan</option>
      <option value="02">Feb</option>
      <option value="03">Mar</option>
      <option value="04">Apr</option>
      <option value="05">May</option>
      <option value="06">Jun</option>
      <option value="07">Jul</option>
      <option value="08">Aug</option>
      <option value="09">Sep</option>
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
</div>

<script src="jquery-3.2.1.min.js"></script>
<script>
  function onMarketSelectChange(e) {
    var market = this.value;
    fetchMarketData(market);
  }

  function fetchMarketData(market) {
    jQuery.ajax({
       type: "GET",
       url: market + ".csv",
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
      var month = dateparts[1];
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
      if(typeof(marketData[year]["01"]) !== "undefined") {
        $('.year-select').append("<option>" + year + "</option>")
      }
    });
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
    for(var i=startYearSelect.value; i <= endYearSelect.value; i++) {
      if(i == startYearSelect.value) {
        months += ( 13 - startMonthSelect.value );
      } else if(i === endYearSelect.value) {
        months += endMonthSelect.value;
      } else {
          months += 12;
      }
    }
    var years = parseFloat( months / 12 );
    var totalReturn = ( ( end / start ) - 1.00 ) * 100.00;
    var annualizedReturn = ( Math.pow(end / start, ( 1.00 / years )) - 1.00 ) * 100.00;
    resultsEl.style.display = '';
    totalReturnEl.innerHTML = roundP(totalReturn, 2) + "%";
    annualizedReturnEl.innerHTML = roundP(annualizedReturn, 2) + "%";
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
  marketSelect.addEventListener('change', onMarketSelectChange);
  calculateForm.addEventListener('submit', onCalculateFormSubmit);
  onMarketSelectChange.call(marketSelect);
</script>
