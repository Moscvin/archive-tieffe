<?php

namespace App\Models;

use App\Models\Operation\Operation;
use App\Models\Operation\OperationMachinery;
use Illuminate\Database\Eloquent\Model;

class Machinery extends Model
{
    protected $table = 'macchinari';
    protected $primaryKey = 'id_macchinario';
    protected $fillable = [
        'id_sedi',
        'descrizione',
        'modello',
        'matricola',
        'anno',
        'note',
        'attivo',
        'tetto',
        '2tecnici',
        'alldata',
        'tipologia',
        "SF_split",
        "SF_canalizzato",
        "SF_predisp_presente",
        "SF_imp_el_presente",
        "SF_mq_locali",
        "SF_altezza",
        "SF_civile",
        "SF_indust_commer",
        "tipo_impianto",
        "SC_posizione_cana",
        "SC_certif_canna",
        "SC_cana_da_intubare",
        "SC_metri",
        "SC_curve",
        "SC_materiale",
        "SC_ind_com",
        "SC_tondo_oval",
        "SC_sezione",
        "SC_tetto_legno",
        "SC_distanze",
        "F_anno_aquisto",
        "F_tipo_gas",
        "C_costruttore",
        "C_matr_anno",
        "C_nominale",
        "C_combustibile",
        "C_tiraggio",
        "C_uscitafumi",
        "C_libretto",
        "C_LA_locale",
        "C_LA_idoneo",
        "C_LA_presa_aria",
        "C_LA_presa_aria_idonea",
        "C_KRA_dimensioni",
        "C_KRA_materiale",
        "C_KRA_coibenza",
        "C_KRA_curve90",
        "C_KRA_lunghezza",
        "C_KRA_idoneo",
        "C_CA_tipo",
        "C_CA_materiale",
        "C_CA_sezione",
        "C_CA_dimensioni",
        "C_CA_lunghezza",
        "C_CA_cam_raccolta",
        "C_CA_cam_raccolta_ispez",
        "C_CA_curve90",
        "C_CA_curve45",
        "C_CA_curve15",
        "C_CA_piombo",
        "C_CA_liberaindipendente",
        "C_CA_innesti",
        "C_CA_rotture",
        "C_CA_occlusioni",
        "C_CA_corpi_estranei",
        "C_CA_cambiosezione",
        "C_CA_restringe",
        "C_CA_diventa",
        "C_CA_provatiraggio",
        "C_CA_tiraggio",
        "C_CA_tettolegno",
        "C_CA_distanze_sicurezza",
        "C_CA_certificazione",
        "C_KCO_dimensioni",
        "C_KCO_forma",
        "C_KCO_cappelloterminale",
        "C_KCO_zonareflusso",
        "C_KCO_graditetto",
        "C_KCO_accessotetto",
        "C_KCO_comignolo",
        "C_KCO_tipocomignolo",
        "C_KCO_idoncomignolo",
        "C_KCO_cestello",
        "C_KCO_ponteggio",
    ];

    public function location() {
        return $this->belongsTo('App\Models\Location', 'id_sedi');
    }

    public function operationMachineries() {
        return $this->hasMany(OperationMachinery::class, 'id_macchinario');
    }

    public function delete()
    {
        $this->operationMachineries->each(function($item) {
            $item->delete();
        });
        return parent::delete();
    }

    public function getFullDescriptionAttribute()
    {
        $description = $this->descrizione;
        if($this->modello) {
            $description .= (' / ' . $this->modello);
        }
        if($this->matricola) {
            $description .= (' / ' . $this->matricola);
        }
        return $description;
    }

    public function operations()
    {
        return $this->belongsToMany(Operation::class, 'interventi_macchinari', 'id_macchinario', 'id_intervento');
    }
}
