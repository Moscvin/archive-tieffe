<?php

namespace App\Http\View\Composers;
use Illuminate\View\View;
use Illuminate\Support\Facades\Config;

class TipologiaComposer
{
    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $view->with([
          'tipologia_intervento' => Config::get('tipologia.types'),
          'tipologia_macchinari' => Config::get('tipologia.types'),
          'tipologia_impianto' => Config::get('tipologia.tipologia_impianto'),
          'tipologia_caldo_ca_tipo' => Config::get('tipologia.C_CA_tipo'),
          'tipologia_caldo_ca_materiale' => Config::get('tipologia.C_CA_materiale'),
          'tipologia_caldo_ca_sezione' => Config::get('tipologia.C_CA_sezione'),
          'tipologia_caldo_ca_provatiraggio' => Config::get('tipologia.C_CA_provatiraggio'),
          'tipologia_caldo_ca_tiraggio' => Config::get('tipologia.C_CA_tiraggio'),
          'tipologia_caldo_ca_distanze_sicurezza' => Config::get('tipologia.C_CA_distanze_sicurezza'),
          'tipologia_caldo_kco_graditetto' => Config::get('tipologia.C_KCO_graditetto'),
          'tipologia_caldo_kco_accessotetto' => Config::get('tipologia.C_KCO_accessotetto'),
          'tipologia_caldo_kco_tipocomignolo' => Config::get('tipologia.C_KCO_tipocomignolo'),
          'tipologia_sopralluogo_caldo_SC_passotetto' => Config::get('tipologia.SC_passotetto'),

      ]);

    }
}
