<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scheda Tecnica Di Intervento</title>
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@400;700&display=swap" rel="stylesheet">
    @include('.style')
</head>

<body>
    <div class="main-wrapper">
        <div class="main-header">
            <div class="main-header__logo">
                <img src="{{asset('img/login_and_email.png')}}" alt="Logo">
            </div>
            <div class="main-header__title">
                <h1>SCHEDA TECNICA DI INTERVENTO</h1>
            </div>
        </div>
        <div class="row row_first">
            <div class="column">
                <h2 class="main-title">TIEFFE S.R.L.</h2>
                <p>Via Emilia Ovest, 1045/1047 - 41049 Modena (MO)</p>
                <p>Tel. 059.9782404 - Tel. e Fax. 059.9782406</p>
                <p>Partita Iva: 03300390360</p>
                <p>www.tieffeimpianti.com - info@tieffeimpianti.com</p>
            </div>
            <div class="column">
                <p>Numero Rapporto: <span class="bold mrb" id="nrIntervention">{{ $report->reportNumber }}</span> del
                    <span class="bold" id="interventionDate">{{ $data_intervento }}</span>
                </p>
                <p>Numero tecnici: <span id="nrTechnicians">{{ $count_technicians }}</span></p>
                <p>Tecnici: <span id="Technicians">{{ $technicians }}</span> </p>
            </div>
        </div>
        <div class="row row_second">
            @if($invoiceTo == 0)
            <div class="column">
                <h2 class="secondary-title">Destinatario della fatturazione</h2>
                <p class="bold"><span id="receptor">{{ $client->ragione_sociale ?? '' }}</span></p>
                <p>@if($client->partita_iva!=null)<span id="receptorIva">P.IVA: {{$client->partita_iva}}</span>@endif -
                    @if($client->codice_fiscale!=null) C.F {{$client->codice_fiscale}} @endif</p>
                <p id="receptorStreet">{{ $location->indirizzo_via }} {{ $location->indirizzo_civico }}</p>
                <p id="receptorCity">{{ $location->indirizzo_cap }} {{ $location->indirizzo_comune }}
                    {{ $location->indirizzo_provincia }}</p>
                <p id="reportEmail">{{ $report->mail_send }}</p>
                @php
                $phones = $location->telefono1 && $location->telefono2?
                  [$location->telefono1, $location->telefono2] :
                  ($location->telefono1? [$location->telefono1] :(
                  $location->telefono2? [$location->telefono2] : []));
                @endphp
                <p id="reportPhone">{{ implode(', ', $phones) }}</p>
            </div>
            @elseif($invoiceTo <> 0)
            <div class="column">
                <h2 class="secondary-title">Destinatario della fatturazione</h2>
                <p class="bold"><span id="receptor">{{ $client->ragione_sociale ?? '' }}</span></p>
                <p>@if($invoice_client->partita_iva!=null)<span id="receptorIva">P.IVA:
                        {{$invoice_client->partita_iva}}</span>@endif - @if($invoice_client->codice_fiscale!=null)
                    C.F {{$invoice_client->codice_fiscale}} @endif</p>
                <p id="receptorStreet">{{ $location->indirizzo_via }} {{ $location->indirizzo_civico }}</p>
                <p id="receptorCity">{{ $location->indirizzo_cap }} {{ $location->indirizzo_comune }}
                    {{ $location->indirizzo_provincia }}</p>
                <p id="reportEmail">{{ $report->mail_send }}</p>
                @php
                $phones = $location->telefono1 && $location->telefono2?
                    [$location->telefono1, $location->telefono2] :
                    ($location->telefono1? [$location->telefono1] :(
                    $location->telefono2? [$location->telefono2] : []));
                @endphp
                <p id="reportPhone">{{ implode(', ', $phones) }}</p>
            </div>
            @endif
            <div class="column column_23">
                <h2 class="secondary-title">Luogo dell’intervento</h2>
                <p id="placeStreet">{{ $report->luogo_intervento }}</p>
                @if($invoiceTo != 0)
                <div>
                    <h2 class="secondary-title">Cliente</h2>
                    <p class="bold"><span id="receptor">{{ $client->ragione_sociale ?? '' }}</span></p>
                    <p>@if($client->partita_iva!=null)<span id="receptorIva">P.IVA:
                            {{$client->partita_iva}}</span>@endif - @if($client->codice_fiscale!=null) C.F
                        {{$client->codice_fiscale}} @endif</p>

                </div>
                @endif
            </div>
            <div class="column column_27 column_nb">
                <div class="warranty">

                    <input type="checkbox" class="chbox-1" {{ $report->garanzia == 1 ? 'checked' : '' }}>
                    <h2 class="secondary-title mr">Garanzia</h2>

                </div>
                <div class="warranty">

                    <input type="checkbox" class="chbox-1" {{ $report->dafatturare == 1 ? 'checked' : '' }}>
                    <h2 class="secondary-title mr">Intervento da fatturare</h2>

                </div>

            </div>
        </div>
        <div class="row row_mec row_mt">
            <div class="column column_nb column_huge">
                <h2 class="secondary-title secondary-title__mb">Macchinario</h2>
                @foreach($report->machineries as $intervent_machinery)
                <div class="row">
                    <div class="column column_nb column_48">
                        <table>
                            <tbody>
                                @if($intervent_machinery->machinery->descrizione)
                                <tr>
                                    <td class="l60">Descrizione</td>
                                    <td class="l40 accent">
                                        <p>{{ $intervent_machinery->machinery->descrizione ?? '-' }}</p>
                                    </td>
                                </tr>
                                @endif
                                @if($intervent_machinery->machinery->SF_split)
                                <tr>
                                    <td class="l60">
                                        <p>Split: </p>
                                    </td>
                                    <td class="l40 accent">{{ $intervent_machinery->machinery->SF_split }}</td>
                                </tr>
                                @endif
                                @if($intervent_machinery->machinery->SF_canalizzato)
                                <tr>
                                    <td class="l60">
                                        <p>Canalizzato:</p>
                                    </td>
                                    <td class="l40 accent">{{ $intervent_machinery->machinery->SF_canalizzato }}</td>
                                </tr>
                                @endif
                                @if($intervent_machinery->machinery->SF_predisp_presente)
                                <tr>
                                    <td class="l60">
                                        <p>Predisposizione presente</p>
                                    </td>
                                    <td class="l40 accent">
                                        {{ $intervent_machinery->machinery->SF_predisp_presente == 2? 'Si' : 'No' }}
                                    </td>
                                </tr>
                                @endif

                                @if($intervent_machinery->machinery->SF_imp_el_presente)
                                <tr>
                                    <td class="l60">
                                        <p>Impianto Elettrico presente</p>
                                    </td>
                                    <td class="l40 accent">
                                        {{ $intervent_machinery->machinery->SF_imp_el_presente == 2? 'Si' : 'No' }}
                                    </td>
                                </tr>
                                @endif

                                <tr>
                                    <td class="l60">
                                        <p>Cestello</p>
                                    </td>
                                    <td class="l40 accent">
                                        {{ $intervent_machinery->machinery->C_KCO_cestello == 2 ? 'Si' : 'No' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="l60">
                                        <p>Ponteggio</p>
                                    </td>
                                    <td class="l40 accent">
                                        {{ $intervent_machinery->machinery->C_KCO_ponteggio == 2 ? 'Si' : 'No' }}
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                    <div class="column column_nb column_accent">
                        <table>
                            <tbody>
                                @if($intervent_machinery->machinery->SF_mq_locali)
                                <tr>
                                    <td class="l60">
                                        <p>Mq Locali</p>
                                    </td>
                                    <td class="l40 accent">
                                        <p>{{ $intervent_machinery->machinery->SF_mq_locali }}</p>
                                    </td>
                                </tr>
                                @endif
                                @if($intervent_machinery->machinery->SF_altezza)
                                <tr>
                                    <td class="l60">
                                        <p>Altezza (metri)</p>
                                    </td>
                                    <td class="l40 accent">
                                        <p>{{ $intervent_machinery->machinery->SF_altezza }}</p>
                                    </td>
                                </tr>
                                @endif
                                @if($intervent_machinery->machinery->SF_civile)
                                <tr>
                                    <td class="l60">
                                        <p>Civile</p>
                                    </td>
                                    <td class="l40 accent">
                                        <p>{{ $intervent_machinery->machinery->SF_civile == 2? 'Si' : 'No' }}</p>
                                    </td>
                                </tr>
                                @endif
                                @if($intervent_machinery->machinery->SF_indust_commer)
                                <tr>
                                    <td class="l60">
                                        <p>Industriale / Commerciale</p>
                                    </td>
                                    <td class="l40 accent">
                                        <p>{{ $intervent_machinery->machinery->SF_indust_commer == 2? 'Si' : 'No' }}</p>
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        <div class="row row_mt row_five">
            <div class="column">
                <h2 class="secondary-title">Materiali</h2>
                <table class="half" id="materials">
                    <thead>
                        <tr>
                            <th class="l10">Q.tà</th>
                            <th>Descrizione</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($intervention_equipment as $equipment)
                        <tr>
                            <td class="l10 right">{{ isset($equipment->quantita) ? $equipment->quantita : '' }}</td>
                            <td>{{ isset($equipment->descrizione) ? $equipment->descrizione : '' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($report->incasso_stato == 1)
            <div class="column" style="{{ $report->incasso_stato == 0 ? 'visibility: hidden;' : '' }}">
                <h2 class="secondary-title">Importi incassati</h2>
                <table class="half" id="hoursKm">
                    <thead>
                        <tr>
                            <th class="l20 center">Importo</th>
                            <th class="l20 center">Euro</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($report->incasso_pos > 0)
                        <tr>
                            <td class="l20 center">Tramite POS</td>
                            <td class="l20 center">{{ $report->incasso_pos }}</td>
                        </tr>
                        @endif
                        @if($report->incasso_in_contanti > 0)
                        <tr>
                            <td class="l20 center">In contanti</td>
                            <td class="l20 center">{{ $report->incasso_in_contanti }}</td>
                        </tr>
                        @endif
                        @if($report->incasso_con_assegno > 0)
                        <tr>
                            <td class="l20 center">Con assegno</td>
                            <td class="l20 center">{{ $report->incasso_con_assegno }}</td>
                        </tr>
                        @endif
                    </tbody>
                    <tfoot>
                        <tr>
                            <td class="l30 center">Totale</td>
                            <td class="l20 center">
                                {{ number_format($report->incasso_pos + $report->incasso_in_contanti + $report->incasso_con_assegno,2,'.',',') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @endif
            @if($report->incasso_stato == 2)
            <div class="column" style="{{ $report->operation->incasso <= 0 ? 'visibility: hidden;' : '' }}">
                <h2 class="secondary-title">L'importo di € {{ $report->operation->incasso }} non è stato incassato</h2>
            </div>
            @endif
            @if($report->incasso_stato == 3)
            <div class="column" style="{{ $report->operation->incasso <= 0 ? 'visibility: hidden;' : '' }}">
                <h2 class="secondary-title">L'importo di € {{ $report->operation->incasso }} verrà pagato con bonifico</h2>
            </div>
            @endif
        </div>
        <div class="row row_mt">
            <div class="column">
                <table class="no_bo">
                  @if($report->linea_vita)
                  <tr>
                      <td>
                          <h2 class="secondary-title">Linea vita:</h2>
                      </td>
                      <td>{{ $report->linea_vita == 2 ?  'Si' : 'No'}}</td>
                  </tr>
                  @endif
                  @if($report->piano_intervento)
                  <tr>
                      <td>
                          <h2 class="secondary-title">Piano dell'intervento:</h2>
                      </td>
                      <td>{{ $report->piano_intervento ?? '' }}</td>
                  </tr>
                  @endif
                  @if($report->carrello_cingolato)
                  <tr>
                      <td>
                          <h2 class="secondary-title">Carrello cingolato:</h2>
                      </td>
                      <td>{{ $report->carrello_cingolato == 2 ?  'Si' : 'No'}}</td>
                  </tr>
                  @endif
                  @if($report->UNI_7129)
                  <tr>
                      <td>
                          <h2 class="secondary-title">Normativa UNI 7129:</h2>
                      </td>
                      <td>{{ $report->UNI_7129 == 2? 'Conforme' : 'Non Conforme'}}</td>
                  </tr>
                  @endif
                  @if($report->UNI_10683)
                  <tr>
                      <td>
                          <h2 class="secondary-title">Normativa UNI 10683:</h2>
                      </td>
                      <td>{{ $report->UNI_10683 == 2? 'Conforme' : 'Non Conforme'}}</td>
                  </tr>
                  @endif
                  @if($report->altra_norma_text && $report->altra_norma_value)
                  <tr>
                      <td>
                          <h2 class="secondary-title">{{ $report->altra_norma_text }}:</h2>
                      </td>
                      <td>{{ $report->altra_norma_value == 2? 'Conforme' : 'Non Conforme'}}</td>
                  </tr>
                  @endif
                  @if($report->raccomandazioni)
                  <tr>
                      <td colspan="2">
                          <h2 class="secondary-title">Raccomandazioni:</h2>
                          <p class="notes">{{ $report->raccomandazioni ?? ''}}</p>
                      </td>
                  </tr>
                  @endif
                  @if($report->prescrizioni)
                  <tr>
                      <td colspan="2">
                          <h2 class="secondary-title">Prescrizioni:</h2>
                          <p class="notes">{{ $report->prescrizioni ?? ''}}</p>
                      </td>
                  </tr>
                  @endif
                  <tr>
                      <td colspan="2">
                          <h2 class="secondary-title">Note</h2>
                          <p class="notes">{{ $note }}</p>
                      </td>
                  </tr>
                </table>
            </div>
            <div class="column column_nob">
                <p>Luogo e data <span
                        id="name">{{ isset($report->intervention->location->indirizzo_comune) ? $report->intervention->location->indirizzo_comune.',' : '' }}
                        {{ $data_intervento ?? '' }}</span></p>

                <div class="{{-- column --}} column-no-cell center sign" id="sign">
                    @if(isset($signature) && $signature && $signatureExists)
                    <img src='{{ asset("file/$signature") }}' class="signature-img" alt="Firma">
                    @endif
                </div>

            </div>
        </div>
    </div>
</body>

</html>
