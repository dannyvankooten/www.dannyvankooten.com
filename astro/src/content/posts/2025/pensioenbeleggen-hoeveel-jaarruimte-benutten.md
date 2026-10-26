---
title: "Pensioenbeleggen: optimaal jaarruimte benutten?"
description: "Hoeveel jaarruimte benutten voor pensioenbeleggen als zelfstandige? Een methode om tot een redelijk bedrag te komen."
date: "2025-10-07T00:00:00+00:00"
modified: "2026-05-14T21:08:34+02:00"
categories: ["Personal"]
---

<style>
    h3 { font-size: 1.1rem; }
    h2 { font-size: 1.2rem; }
</style>

> This post is in Dutch because it is specific to the Dutch retirement system.

<div style="background: #2a2f34; padding: 1rem;">
<h3 style="margin-top: 0;">Inhoudsopgave</h3>
<a href="#wat-is-jaarruimte">Wat is jaarruimte?</a><br>
<a href="#hoe-benut-je-jaarruimte-optimaal">Hoe benut je je jaarruimte optimaal?</a><br>
<a href="#math">Hoeveel moet je dan inleggen?</a><br>
<a href="#calculator">Calculator: bepalen periodieke inleg</a>
</div>

Als zelfstandige kan het lastig zijn om te bepalen hoeveel je opzij moet zetten om later voldoende pensioen op te bouwen. Een veelgehoorde vuistregel is **5 tot 15% van je bruto inkomen**. Maar als je inkomen jaarlijks sterk varieert, of als je pas later begint met pensioenbeleggen, zegt die vuistregel weinig.

Dan blijft de vraag hoeveel jaarruimte je jaarlijks wilt benutten. Dit artikel is geen financieel advies, maar een praktische rekenmethode om tot een redelijk bedrag te komen.

## Wat is jaarruimte?

Jaarruimte is een fiscaal vriendelijke manier om vermogen op te bouwen dat je na je pensioendatum periodiek kunt laten uitkeren. In essentie gebruik je het om inkomen van nu naar na je pensioendatum te verplaatsen. Dat heeft zowel voor- als nadelen:

### Voordelen van jaarruimte

-   Het geld dat je op een geblokkeerde pensioenrekening stort, kun je aftrekken van je belastbaar inkomen in box 1. Na je pensioendatum betaal je hier alsnog belasting over, maar waarschijnlijk tegen een lager tarief (momenteel 17,92% vs. 35,82% t/m €38.441).
-   Je bespaart mogelijk jarenlang op box 3-belasting.

### Nadelen van jaarruimte

-   Het geld staat vast tot ongeveer je AOW-datum, tenzij je bereid bent revisierente te betalen.
-   Je moet met het geld dat vrijkomt een periodieke uitkering aankopen.
-   Belastingregels kunnen in de tussentijd veranderen.

## Hoe benut je je jaarruimte optimaal?

Daar is geen eenduidig antwoord op te geven. Veel hangt af van je persoonlijke omstandigheden.

De grootste voordelen van jaarruimte benutten zijn het verplaatsen van inkomen naar een hopelijk lager belastingtarief en het besparen op box 3-belasting wanneer je boven het heffingsvrij vermogen zit.

Met de huidige belastingregels betaal je na je AOW-datum 17,92% box 1-belasting in de eerste schijf. Daarom is het meestal verstandig om in elk geval pensioeninkomen op te bouwen tot maximaal €38.441. Dat bedrag is inclusief AOW en eventuele andere inkomsten.

Bij een pensioeninkomen boven €38.441 zijn de belastingvoordelen beperkter. Dan spelen je persoonlijke omstandigheden een grotere rol, zoals in welke box 1-schijf je huidige inkomen valt, of je boven het heffingsvrij vermogen in box 3 zit en hoeveel waarde je hecht aan flexibiliteit.

## Hoeveel moet je dan inleggen?

Concreet wil je weten hoeveel pensioeninkomen je ongeveer nodig hebt, welk eindbedrag daarbij hoort en hoeveel je tot je pensioendatum jaarlijks moet inleggen om op dat bedrag uit te komen.

<p>We kunnen een conservatieve schatting van het benodigde eindkapitaal <var>FV</var> berekenen door de looptijd te vermenigvuldigen met het deel van het inkomen waar we zelf voor moeten zorgen:</p>

<math display="block">
<mrow>
<mi>FV</mi>
<mo>=</mo>
<mi>looptijd</mi>
<mo>⋅</mo>
<mo>(</mo>
<mi>gewenst inkomen</mi>
<mo>–</mo>
<mi>AOW</mi>
<mo>)</mo>
</mrow>
</math>

<p>Vervolgens berekenen we de benodigde maandelijkse inleg als een annuïteit. Daarbij is de looptijd <var>n</var> gelijk aan het aantal jaren tot je pensioendatum, gebruiken we de huidige waarde <var>PV</var> van je pensioenpot als startpunt en nemen we het verwachte jaarlijkse rendement op je beleggingen <var>r</var> als rente.</p>

<math display="block" xmlns="http://www.w3.org/1998/Math/MathML">
<mrow>
<mi>PMT</mi>
<mo>=</mo>
<mo>–</mo>
<mfrac>
<mrow>
<mi>r</mi>
<mo>×</mo>
<mfenced>
<mrow>
<mi>PV</mi>
<mo>⁢</mo>
<msup>
<mrow>
<mo>(</mo>
<mrow>
<mn>1</mn>
<mo>+</mo>
<mi>r</mi>
</mrow>
<mo>)</mo>
</mrow>
<mi>n</mi>
</msup>
<mo>+</mo>
<mi>FV</mi>
</mrow>
</mfenced>
</mrow>
<mrow>
<msup>
<mrow>
<mo>(</mo>
<mrow>
<mn>1</mn>
<mo>+</mo>
<mi>r</mi>
</mrow>
<mo>)</mo>
</mrow>
<mi>n</mi>
</msup>
<mo>–</mo>
<mn>1</mn>
</mrow>
</mfrac>
</mrow>
</math>

Je kunt dit uitrekenen met een rekenmachine of spreadsheet en de `PMT`\-functie gebruiken, maar de tool hieronder maakt het eenvoudiger.

Zo zie je snel of je op schema zit, extra moet inleggen of mogelijk al meer spaart dan nodig is.

## Berekenen maandelijkse inleg pensioenrekening

<style>
#calculator {
    padding: 1rem;
    background: #2a2f34;
}
#calculator label { line-height:38px; }
#calculator .row {
    margin-bottom: 1.5rem;
}
@media (min-width: 720px) {
    #calculator .row {
        display: flex;
        flex-flow: row wrap;
        margin-bottom: 1rem;
    }
    #calculator .col {
        width: 66.6666%;
    }
    #calculator .col-1 {
        width: 33.333%;
    }
}
#calculator input {
    font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", "Noto Sans", "Liberation Sans", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
    display: inline-block;
    width: 240px;
    padding: .375rem .75rem;
    font-size: 1rem;
    font-weight: 400;
    line-height: 1.5;
    color: #dee2e6;
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    background-color: #212529;
    background-clip: padding-box;
    border: 1px solid #495057;
    border-radius: 0.375rem;
    transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
    margin-right: 0.5rem;
}
#calculator .help {
    font-size: 14px;
    color: #AAA;
    margin-top: 0.5rem;
}
</style>

<form id="calculator">
<div class="row">
<div class="col col-1"><label for="date-aow">AOW datum</label></div>
<div class="col">
<input id="date-aow" name="date-aow" type="date" value="2060-01-01">
<div class="help"><a href="https://www.rijksoverheid.nl/onderwerpen/algemene-ouderdomswet-aow/vraag-en-antwoord/wanneer-gaat-mijn-aow-in">Wanneer gaat mijn AOW in? (rijksoverheid.nl)</a></div>
</div>
</div>
<div class="row">
<div class="col col-1"><label for="retirement-income">Gewenst bruto inkomen <sup>1</sup></label></div>
<div class="col">
<input id="retirement-income" min="0" name="retirement-income" step="100" type="number" value="38441"> €
            <div class="help">
                Zie ook <a href="https://www.belastingdienst.nl/wps/wcm/connect/bldcontentnl/belastingdienst/prive/inkomstenbelasting/heffingskortingen_boxen_tarieven/boxen_en_tarieven/box_1/box_1">Box 1: belastbaar inkomen uit werk en woning (belastingdienst.nl)</a>
</div>
</div>
</div>
<div class="row">
<div class="col col-1"><label for="aow-income">Verwacht inkomen uit AOW</label></div>
<div class="col">
<input id="aow-income" min="0" name="aow-income" step="100" type="number" value="14100"> €
            <div class="help">
                Hoeveel inkomen verwacht je jaarlijks vanuit AOW en andere pensioenvoorzieningen? Zie <a href="https://www.mijnpensioenoverzicht.nl/nl">mijnpensioenoverzicht.nl</a>
</div>
</div>
</div>
<div class="row">
<div class="col col-1"><label for="present-value">Huidige waarde</label></div>
<div class="col">
<input id="present-value" min="0" name="present-value" type="number" value="50000"> €
            <div class="help">
                Wat is de huidige waarde van je pensioenbeleggen rekening(en)?
            </div>
</div>
</div>
<div class="row">
<div class="col col-1"><label for="duration">Gewenste looptijd</label></div>
<div class="col">
<input id="duration" max="30" min="5" name="duration" type="number" value="20"> jaar
            <div class="help">
                Voor hoeveel jaar wil je een inkomen kopen?<br>
                Bij de meeste aanbieders van lijfrente producten geldt hier een minimum van 5 en een maximum van 20 jaar.
            </div>
</div>
</div>
<div class="row">
<div class="col col-1"><label for="expected-real-return">Verwacht rendement</label></div>
<div class="col">
<input id="expected-real-return" min="0.0" name="expected-real-return" step="0.5" type="number" value="4.0">%
            <div class="help">
                Je verwachte rendement op jaarbasis na inflatie. 4% is een degelijk lange termijn gemiddelde voor een wereldwijde mix van 60% aandelen en 40% obligaties.
            </div>
</div>
</div>
<div class="row" style="margin-bottom:0;">
<div class="col col-1"></div>
<div class="col">
            € <span id="result" style="font-weight:bold; font-size: 2rem;"></span> inleg per maand
        </div>
</div>
</form>

<div class="text-muted">
<p><sup>1</sup> Het belastingvoordeel voor het benutten van jaarruimte boven een pensioeninkomen van €38.441 (de eerste schijf in box 1) is beperkt. Het kan voordeliger en bovendien flexibeler zijn om daarboven in box 3 of een spaar-BV te beleggen voor je pensioen.</p>
</div>

De informatie op deze site is mijn persoonlijke mening, geen financieel advies. Je blijft zelf verantwoordelijk voor je eigen keuzes.

<script>
function pmt(ir, np, pv, fv, type) {
    /*
     * ir   - interest rate per month
     * np   - number of periods (months)
     * pv   - present value
     * fv   - future value
     * type - when the payments are due:
     *        0: end of the period, e.g. end of month (default)
     *        1: beginning of period
     */
    var pmt, pvif;

    fv || (fv = 0);
    type || (type = 0);

    if (ir === 0) {
        return -(pv + fv)/np;
    }

    pvif = Math.pow(1 + ir, np);
    pmt = - ir * (pv * pvif + fv) / (pvif - 1);

    if (type === 1) {
        pmt /= (1 + ir);
    }

    return pmt;
}

function getMonthDifference(startDate, endDate) {
  return (
    endDate.getMonth() -
    startDate.getMonth() +
    12 * (endDate.getFullYear() - startDate.getFullYear())
  );
}

function runCalculator() {
    let presentValue = parseInt(form.elements.namedItem('present-value').value);
    let expectedRealReturn = parseFloat(form.elements.namedItem('expected-real-return').value) / 100.0;
    let duration = parseInt(form.elements.namedItem('duration').value);
    let targetDate = new Date(form.elements.namedItem('date-aow').value);
    let retirementIncome = parseInt(form.elements.namedItem('retirement-income').value);
    let aowIncome = parseInt(form.elements.namedItem('aow-income').value);
    let monthsRemaining = getMonthDifference(new Date(), targetDate);
    let futureValue = (retirementIncome - aowIncome) * duration;
    let monthlyContribution = -pmt(expectedRealReturn/12.0, monthsRemaining, -presentValue, futureValue);
    document.querySelector('#result').textContent = Math.ceil(monthlyContribution / 10) * 10;
}

const form = document.querySelector('#calculator');
form.addEventListener('input', runCalculator);
form.addEventListener('change', runCalculator);
runCalculator();
</script>
