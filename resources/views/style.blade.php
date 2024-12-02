<style>
    * {
        box-sizing: border-box;
    }

    h1,
    h2,
    h3,
    h4,
    h5,
    h6 {
        margin: 0;
        padding: 0;
    }

    html {
        font-size: 15px;
        color: black;
        font-family: 'Source Sans Pro', 'Open Sans', sans-serif;
    }

    img {
        max-width: 100%;
        height: auto;
    }

    ul {
        margin: 0;
        padding: 0;
        list-style-type: none;
    }

    p {
        margin: 0;
        padding: 0;
        line-height: 0.9 !important;
    }

    .no_bo td {
        border: none;
    }

    .hidden {
        display: none;
    }


    input[type="submit"]:disabled {
        cursor: not-allowed !important;
        opacity: .6 !important;
    }

    a {
        color: inherit;
        text-decoration: none;
    }



    .main-wrapper {
        max-width: 1200px;
        width: 100%;
        margin: 0.5cm;
        margin: 0 auto;
        padding-top: 10px;
    }

    .main-header {
        margin-bottom: 25px;
        display: table;
        width: 100%;
    }

    .main-header__logo {
        display: table-cell;
    }

    .main-header__logo img {
        height: 42px;
        max-width: none;
        width: 136px;
    }

    .main-header__title {
        display: table-cell;
        vertical-align: middle;
        font-size: 11px;
        font-weight: 700;
        width: 49.5%;
        color: #2e3192;
        text-align: center;
    }

    .row {
        margin-bottom: 20px;
        display: table;
        width: 100%;
    }

    .row_mt {
        margin-top: 40px;
        margin-bottom: 0;
    }

    .column {
        display: inline-block;
        border-left: 1px solid #ef444e;
        display: table-cell;
        padding-left:15px;
        width: 50%;
        /* height: 100%; */

    }

    .column-no-cell {
        display: inline-block;
        border-left: 1px solid #ef444e;
        padding-left:15px;
        width: 50%;
        /* height: 100%; */
    }

    .main-title {
        font-size: 20px;
        color: #2e3192;
        font-weight: 700;
        margin: 0 0 5px;
    }

    .secondary-title {
        font-size: 18px;
        font-weight: 700;
        color: #2e3192;
        margin: 0 0 5px;
    }

    .table-title {
        margin-top: 10px;
        font-size: 13px !important;
        padding: 5px 10px 9px;
        background-color: #fcd9db;
    }

    .bold {
        font-weight: 700;
    }

    .row_first .column {
        vertical-align: middle;
    }

    .row_first p {
        color: #2e3192;
    }

    .row_first span {
        color: black;
    }

    .row_last .column {
        border-left: none;
    }

    .row_last p {
        font-weight: 700;
        color: #2e3192;
    }

    .row_last span {
        font-weight: 400;
        color: black;
        margin-left: 20px;
    }



    .column_huge {
        width: 100%;
    }

    .column_big {
        width: 69%;
    }

    .column_23 {
        width: 23%;
    }

    .column_27 {
        width: 27%;
    }

    .column_48 {
        width: 47.5%;
    }

    .column_little {
        width: 25%;
        vertical-align: middle;
    }

    .column_top {
        vertical-align: top;
    }

    .column_center {
        vertical-align: middle;
    }

    .column_nb {
        border: none;
    }

    .mr {
        margin-right: 5px;
    }

    .mrb {
        margin-right: 50px;
    }

    table {
        width: 100%;
        padding-right: 15px;
    }

    th {
        color: #2e3192;
        font-weight: normal;
        border-bottom: 1px solid #fcd9db;
        border-right: 1px solid #fcd9db;
        line-height: 0.8 !important;
    }

    td {
        border-bottom: 1px solid #fcd9db;
        border-right: 1px solid #fcd9db;
        height: 15px;
        /*height: 23px;*/
        padding: 2px 5px;
        line-height: 0.8 !important;
    }

    tfoot {
        background-color: #fae5e6;
        font-weight: 700;
        text-transform: uppercase;
    }

    th:last-child,
    td:last-child {
        border-right: none;
    }


    .l20 {
        width: 15%;
    }

    .center {
        text-align: center;
    }


    .l10 {
        width: 10%;
    }



    .l60 {
        width: 60%;
    }

    .right {
        text-align: right;
    }

    .accent {
        color: #2e3192;
    }

    .column_nob {
        border-left: none;
        color: #2e3192;
        font-size: 15px;
    }

    .column_nob p {
        margin-bottom: 10px;
    }

    .sign img {
        max-width: 200px;
    }

    #sign {

        padding-top: 9em;
        padding-left: 10em;
    }

    .warranty {
        display: table;
        border-left: 1px solid #ef444e;
        padding-left: 15px;
    }

    .warranty h2,
    .warranty span,
    .warranty .chbox-1 {
        display: table-cell;
        vertical-align: middle;
    }

    .warranty h2 {
        position: relative;
        padding-bottom: 4px;
    }

    .warranty .chbox-1 {
        padding-right: 20px;
    }

    /* @media print { */
    html {
        font-size: 12px;
    }

    .main-wrapper {
        padding-top: 0;
        margin: 0 auto;
    }

    .main-header {
        margin-bottom: 10px;
    }

    .main-header__title {
        font-size: 8px;
    }

    .main-title {
        font-size: 15px;
    }

    .secondary-title {
        font-size: 14px;
        margin-bottom: 0px;
    }

    .secondary-title__mb {
        margin-bottom: 20px;
    }

    .row {
        margin-bottom: 10px;
    }

    .row_mt {
        margin-top: 30px;
    }

    .row_last {
        margin-top: 20px;
        margin-bottom: 0;
    }

    .row_mec {
        border-left: 1px solid #ef444e;
        padding-left: 15px;
    }

    .pageRow{

        page-break-after: always;
    }

    .row_met {
        margin-bottom: 40px;
    }

    .column_nob p {
        margin-bottom: 5px;
    }

    .sign img {
        max-width: 150px;
    }

    .column_nob {
        font-size: 13px;
    }

    .column_accent {
        border-left: 1px solid #ef444e;
    }

    #name {
        color: black;
    }

    .date-column {
        padding-top: 5.5em;
    }

    .signature-img {
        width: 156px;
    }




    p.notes {
        @if($note_length>=120) font-size: 10px;

        @elseif($note_length>=60) font-size: 11px;

        @endif
    }
</style>
