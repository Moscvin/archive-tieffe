<?php

namespace App\Http\Controllers\Addresses;

use App\Http\Controllers\MainController;
use App\Models\Addresses\Comuni;
use App\Models\Addresses\Province;
use Carbon\Carbon;
use Excel;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Storage;

class ComuniController extends MainController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(Request $request, Input $input, $perpage = 10)
    {
        $this->data['input'] = $input;
        $this->data['province'] = Province::all();
        $chars = preg_split('//', \Request::get('permissionAttribute'), -1, PREG_SPLIT_NO_EMPTY);
        $this->data['chars'] = $chars;
        $this->data['pages'] = Comuni::paginate($perpage);
        $this->data['per_page'] = $perpage;


/// FILTER FORM

        $comune = strip_tags(Input::get('comune'));
        $cap = strip_tags(Input::get('cap'));
        $provincia = strip_tags(Input::get('provincia'));
        if (Input::has('comune') OR Input::has('cap') OR Input::has('provincia')) {

            $comunes = Comuni::whereHas('provice', function ($query) use ($provincia) {
                if (!empty($provincia)) {
                    $query->where('id_provincia', $provincia);
                }
            });

            if (!empty($cap)) {
                $comunes->where('cap', 'LIKE', "%$cap%");
            }
            if (!empty($comune)) {
                $comunes->where('comune', 'LIKE', "%$comune%");
            }
            $this->data['pages'] = $comunes->paginate($perpage);
            if ($this->data['pages']->count() == 0) {
                $this->data['pages'] = [];
                return view('addresses.comuni', $this->data);
            }

        }


        if (Input::get('search_input')) {
            $value = Input::get('search_input');

            if (Province::where('sigla_provincia', 'LIKE', "%$value%")->count() > 0) {

                $this->data['pages'] = Comuni::whereHas('provice', function ($query) use ($value) {
                    $query->where('sigla_provincia', 'LIKE', "%$value%");
                })->paginate($perpage);

            } else if (Comuni::where('comune', 'LIKE', "%$value%")->count() > 0) {

                $this->data['pages'] = Comuni::where('comune', 'LIKE', "%$value%")->paginate($perpage);

            } else if (Comuni::where('cap', 'LIKE', "%$value%")->count() > 0) {

                $this->data['pages'] = Comuni::where('cap', 'LIKE', "%$value%")->paginate($perpage);

            } else {
                $this->data['pages'] = [];
                return view('addresses.comuni', $this->data);
            }


        }

        if (Input::has('export')) {
            $dd = $this->data['pages'];
            $file = $this->downloadEXCEL($request, $dd);

            return response()->download($file)->deleteFileAfterSend(true);
        }


        return view('addresses.comuni', $this->data);

    }

    public function downloadEXCEL(Request $request, $data)
    {

        foreach ($data as $page) {

            $dataArray[] = [
                'Comune' => $page->comune,
                'CAP' => $page->cap,
                'Provincia' => $page->provice->sigla_provincia,


            ];


        }


        $exp = Excel::create('EthanPlantsWebApp' . Carbon::now(), function ($excel) use ($dataArray) {
            // Set the title
            $excel->setTitle('DB trattamentu');

            $excel->sheet('trattameni', function ($sheet) use ($dataArray) {


                $sheet->cell('A1', function ($cell) {
                    // manipulate the cell
                    $cell->setValue('Comune');

                });
                $sheet->cell('B1', function ($cell) {
                    // manipulate the cell
                    $cell->setValue('CAP');

                });
                $sheet->cell('C1', function ($cell) {
                    // manipulate the cell
                    $cell->setValue('Provincia');

                });

                $sheet->cells('A1:C1', function ($cells) {

                    $cells->setFontWeight('bold');
                    $cells->setBorder('solid', 'solid', 'solid', 'solid');

                });

                $sheet->fromArray($dataArray, null, 'A2', false, false);
            });


        })->store('xlsx', false, true);

        $file = storage_path('exports/' . $exp['file']);

        return $file;


    }

    public function add($id = null, Request $request)
    {
        $this->data['pages'] = [];
        $this->data['provinces'] = Province::all();

        if (Input::get('save'))
            return $this->save($id, $request);

        if (!empty($id)) {

            $this->data['pages'] = Comuni::where('id_comune', '=', $id)
                ->limit(1)
                ->get();

            if ($this->data['pages']->count() == 0) {
                return view('error_autorization');
            }

        }


        return view('addresses.comuni_add', $this->data);
    }

    public function save($id = null, $request)
    {
        $validator = \Validator::make($request->all(), [
            'comune' => 'required|max:100',
            'id_provincia' => 'required',
            'cap' => 'required|max:5',
        ]);


        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator->errors());
        }

        $data = [
            'comune' => $request->comune,
            'id_provincia' => $request->id_provincia,
            'cap' => $request->cap,
        ];

        if (empty($id)) {

            $action_page = Comuni::create($data);
            Session::flash('success', 'Comuni aggiunta correttamente!');

        } else {

            $action_page = Comuni::where('id_comune', $id)->update($data);
            Session::flash('success', 'Le modifiche sono state correttamente salvate!');

        }

        return redirect('/comuni');
    }

    public function delete()
    {
        $id = Input::get('id');
        if (!empty($id)) {

            Comuni::where('id_comune', '=', $id)->limit(1)->delete();

        }

        return response()->json(array('statut' => 'Success'), 200);
    }

    public function api(Request $request)
    {

        if (isset($request->phrase)) {
            if ($request->input('query') != 'empty') {
                if (isset($request->cap)) {

                    $comunes = Comuni::where('cap', 'LIKE', "$request->phrase%");
                    if ($comunes->count() == 0) {

                        return response()->json(['data' => []]);
                    } else {

                        foreach ($comunes->get() as $comune) {
                            $data[] = [
                                'cap' => $comune->cap,
                                'comune' => $comune->comune,
                                'provincia' => $comune->provice->sigla_provincia
                            ];
                        }
                        return response()->json($data);
                    }

                } else {
                    $comunes = Comuni::where('comune', 'LIKE', "$request->phrase%");

                    if ($comunes->count() == 0) {

                        return response()->json(['data' => []]);
                    } else {

                        $collection = collect($comunes->get());
                        $collection->unique('comune');

                        foreach ($collection->unique('comune')->all() as $comune) {
                            $data[] = [
                                'comune' => $comune->comune,
                                'provincia' => $comune->provice->sigla_provincia
                            ];
                        }
                        return response()->json($data);
                    }
                }
            }
        } else {
            return response()->json(['data' => []]);
        }


    }
}
