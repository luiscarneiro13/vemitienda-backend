<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Fechas;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PaymentRequest;
use App\Models\Payment;
use App\Models\PaymentDetails;
use App\Models\PaymentMethod;
use App\Models\Plan;
use App\Models\PlanUser;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentsController extends Controller
{

    private $pagado;
    private $months;

    public function __construct()
    {
        $this->pagado = [
            (object)['id' => 1, 'label' => 'Pagado'],
            (object)['id' => 0, 'label' => 'Por Pagar']
        ];
        $this->months = [
            (object)['id' => 1, 'label' => 1],
            (object)['id' => 2, 'label' => 2],
            (object)['id' => 3, 'label' => 3],
            (object)['id' => 4, 'label' => 4],
            (object)['id' => 5, 'label' => 5],
            (object)['id' => 6, 'label' => 6],
            (object)['id' => 7, 'label' => 7],
            (object)['id' => 8, 'label' => 8],
            (object)['id' => 9, 'label' => 9],
            (object)['id' => 10, 'label' => 10],
            (object)['id' => 11, 'label' => 11],
            (object)['id' => 12, 'label' => 12],
            (object)['id' => 24, 'label' => 24],
        ];
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $filtrar = request()->get('query');

        $datos['infoData'] = Payment::select([
            'payments.id',
            'users.email',
            'payments.start_date as inicio',
            'payments.end_date as fin',
            DB::raw("if(paid_out=1,'Pagado','...') as pagado")
        ])
            ->leftJoin('users', 'payments.user_id', '=', 'users.id')
            ->when($filtrar, function ($q) use ($filtrar) {
                $q->where('users.name', 'like', '%' . $filtrar . '%');
                $q->orWhere('users.email', 'like', '%' . $filtrar . '%');
                $q->orWhere('payments.start_date', 'like', '%' . $filtrar . '%');
                $q->orWhere('payments.end_date', 'like', '%' . $filtrar . '%');
                return $q;
            })
            ->orderBy('id', 'desc')
            ->paginate(9);

        $datos['token'] = csrf_token();

        $data['data'] = $datos;

        return view('Admin.Payments.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['users'] = User::select('id', 'email as label')->get();
        $data['plans'] = Plan::select('id', 'name as label')->get();
        $data['paymentmethods'] = PaymentMethod::select('id', 'name as label')->get();
        $data['months'] = $this->months;
        return view('Admin.Payments.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PaymentRequest $request)
    {
        $user_id = (int)request()->user_id;
        $months = (int)request()->months;
        $plan_id = (int)request()->plan_id;
        $start_date = Carbon::parse(now());
        $end_date = Carbon::parse(now())->addMonths($months);

        $planUser = PlanUser::where('user_id', $user_id)->first();
        $planUser->plan_id = $plan_id;
        $planUser->start_date = $start_date;
        $planUser->end_date = $end_date;
        $planUser->save();

        $inserts = [
            'user_id' => $user_id,
            'plan_id' => $plan_id,
            'quantity_months' => $months,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'paid_out' => 1,
        ];

        $payment = Payment::create($inserts);
        $payment->save();

        PaymentDetails::create([
            'payment_id' => $payment->id,
            'payment_method_id' => request()->payment_method_id,
            'reference_number' => request()->reference_number,
            'verified' => 1,
            'price' => request()->price
        ]);

        return redirect()->route('payments.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $pay = Payment::find($id);
        $pay->delete();
        return redirect()->route('payments.index');
    }
}
