<style>
    
.yellow-cell{
    background-color:#ebff00;
}


.blue-cell{
    background-color:#2ac1ff;
}

.green-cell{
    background-color:#61ffc7;
}

</style>

<div class="container full-width-container">

<div id="results" class="row">
    <div class="col-xs-12">
        <div class="row">
            <div class="col-xs-12">

                <div class="table-responsive report-component" name="table" template="table">
                    <table id="report-table" class="table table-striped table-bordered table-hover">

                        <thead>
                            <tr style="border-bottom:1px solid #000">
                                <td class="report-table-th" ><b>Cod.</b></td>
                                <td class="report-table-th" ><b>Descrizione</b></td>
                                <td class="report-table-th" ><b>Q.tà</b></td>
                                <td class="report-table-th" ><b>Prezzo netto</b></td>
                                <td class="report-table-th" ><b>U.m.</b></td>
                                <td class="report-table-th" ><b>Sconti</b></td>
                                <td class="report-table-th" ><b>Iva</b></td>
                                <td class="report-table-th" ><b>Mag.</b></td>
                                <td class="report-table-th" ><b>Importo</b></td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="results-cell">

                                <td class="report-table-cell">
                                        <p></p>
                                </td>

                                <td class="report-table-cell" width="35" >
                                        <p>{{ $report['desc_interv'] }}</p>
                                </td>

                                <td class="report-table-cell" width="8" >
                                       {{ $report['nr_ore'] }}
                                </td>

                                <td class="report-table-cell"  width="20" data-format="€#,##0.00">
                                        26.00
                                </td>

                                <td class="report-table-cell" >
                                        <p>ore</p>
                                </td>

                                <td class="report-table-cell">
                                        <p></p>
                                </td>

                                <td class="report-table-cell" >
                                        <p>NS17</p>
                                </td>

                                <td class="report-table-cell" width="5" >
                                        <p>No</p>
                                </td>

                                <td class="report-table-cell"  data-format="€#,##0.00">
                                       =C2*D2
                                </td>

                            </tr>


                            @php

                            foreach($reports_mat as $rm){

                                $materiali=DB::table('materiali')->select('*')->where(['id_materiali'=>$rm['id_materiali']])->first();
                                $raporto = DB::table('rapporti')->select('*')->where(['id_rapporto'=>$rm['id_rapporto']])->first();


                                echo('<tr class="results-cell">

                                    <td class="report-table-cell">
                                            <p></p>
                                    </td>

                                    <td class="report-table-cell" width="35" >
                                            <p>'.$materiali->denominazione_materiali.'</p>
                                    </td>

                                    <td class="report-table-cell" width="8" >
                                          '.$rm->quantita.'
                                    </td>

                                    <td class="report-table-cell" width="20" data-format="€#,##00">
                                            <p></p>
                                    </td>

                                    <td class="report-table-cell" >
                                            <p>nr</p>
                                    </td>

                                    <td class="report-table-cell">
                                            <p></p>
                                    </td>

                                    <td class="report-table-cell" >
                                            <p>NS17</p>
                                    </td>

                                    <td class="report-table-cell" width="5" >
                                            <p>No</p>
                                    </td>

                                    <td class="report-table-cell" data-format="€#,##00">
                                            
                                    </td>

                                </tr>');

                            }

                            @endphp
                        </tbody>
                    </table>
                </div>




            </div>
        </div>

    </div>
</div>

