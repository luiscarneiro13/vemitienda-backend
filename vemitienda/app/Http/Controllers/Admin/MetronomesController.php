<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\WEB\MetronomeRequest;
use App\Models\Metronome;
use App\Repositories\MetronomesRepository;

class MetronomesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $datos['infoData'] = MetronomesRepository::getMetronomesUser(9);
        $data['data'] = $datos;

        return view('Admin.Metronomes.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('Admin.Metronomes.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\WEB\MetronomeRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MetronomeRequest $request)
    {
        MetronomesRepository::storeMetronome();

        return redirect()->route('metronomos.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $metronome = Metronome::findOrFail($id);
        $this->authorize('update', $metronome);

        $data['metronome'] = $metronome;

        return view('Admin.Metronomes.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\WEB\MetronomeRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(MetronomeRequest $request, $id)
    {
        $metronome = Metronome::findOrFail($id);
        $this->authorize('update', $metronome);

        MetronomesRepository::updateMetronome($metronome);

        return redirect()->route('metronomos.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $metronome = Metronome::findOrFail($id);
        $this->authorize('delete', $metronome);

        MetronomesRepository::deleteMetronome($metronome);

        return redirect()->route('metronomos.index');
    }
}
