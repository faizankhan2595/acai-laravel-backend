<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\PointTransaction;
use App\User;
use DataTables;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PointTransactionController extends Controller
{
    public function index(Request $request,$id=0)
    {
        $data = PointTransaction::where('user_id', $id)->with('user:id,name');
        $data2 = PointTransaction::where('user_id', $id)->orderBy('created_at', 'asc')->with('user:id,name')->get();

        $old_balance = 0;
        $expired_point_ids = [];

        $is_jan_update_considered = false;

        foreach ($data2 as $key => $point_transaction) {
            $point_transaction->points_available = $point_transaction->transaction_value;
            $point_transaction->points_redeemed = 0;
            $point_transaction->expired_points = 0;
            $point_transaction->old_balance = 0;
        }

        $numItems = count($data2);
        $i = 0;

        foreach ($data2 as $key => $point_transaction) {
            $date = Carbon::parse($point_transaction->created_at);

            if ($date->gte(Carbon::parse('2022-01-02 23:58:59'))) {
                if(!$is_jan_update_considered) {
                    if($old_balance<0) {
                        $old_balance = 0;
                        $is_jan_update_considered = true;
                    }
                }
            } 

            if ($point_transaction->transaction_type == 1) {
                $old_balance = $old_balance + $point_transaction->transaction_value;
            } else {
                $total_points_redeemed = 0;
                $date = Carbon::parse($point_transaction->created_at);

                if($date->gte(Carbon::parse('2022-01-17 00:00:00'))) {
                    $date = $date->endOfDay();
                }
                
                $rows = [];

                foreach ($data2 as $key => $value) {
                    if ($value->transaction_type == 1 && Carbon::parse($value->expiring_on)->gte($date)) {
                        array_push($rows, $value);
                    }
                }

                $amount_to_redeem = (int) $point_transaction->transaction_value;
                
                $amount_redeemed_map = array();

                foreach ($rows as $row) {
                    $amount_redeemed = min($row->points_available, $amount_to_redeem);

                    $amount_redeemed_map[$row->id] = $amount_redeemed;
                    $amount_to_redeem -= $amount_redeemed;

                    if ($amount_to_redeem == 0) {
                        break;
                    } 
                }

                foreach ($amount_redeemed_map as $tid => $tr_amt) {
                    $transaction = $data2->where('id', $tid)->first();
                    $temp_points_redeemed  = $transaction->points_redeemed + $tr_amt;
                    $temp_points_available = $transaction->transaction_value - $temp_points_redeemed;

                    $total_points_redeemed += $tr_amt;
                    

                    foreach ($data2 as $key2 => $value2) {
                        if($value2->id == $tid){
                            $data2[$key2]->points_redeemed = $temp_points_redeemed;
                            $data2[$key2]->points_available = $temp_points_available;
                        }
                    }
                }
                
                $old_balance = $old_balance - $point_transaction->transaction_value;
            }

            $expired_points = 0;
            $expired_point_id = '';

            if(++$i === $numItems) {
                foreach ($data2 as $key => $value) {
                    if ($value->transaction_type == 1 && Carbon::parse($value->expiring_on)->lt(Carbon::now())) {
                        if($value->points_available > 0){
                            if(!in_array($value->id, $expired_point_ids)) {
                                $expired_points += $value->points_available;
                                array_push($expired_point_ids, $value->id);
                                $expired_point_id = $expired_point_id.$value->id.' ';
                            }
                        }
                    }
                }
            } else {
                foreach ($data2 as $key => $value) {
                    if ($value->transaction_type == 1 && Carbon::parse($value->expiring_on)->lt($date)) {
                        if($value->points_available > 0){
                            if(!in_array($value->id, $expired_point_ids)) {
                                $expired_points += $value->points_available;
                                array_push($expired_point_ids, $value->id);
                                $expired_point_id = $expired_point_id.$value->id.' ';
                            }
                        }
                    }
                }
            }

            if($expired_points>0) {
                if(($old_balance - $expired_points)<0) {
                    $old_balance = 0;
                } else {
                    $old_balance = $old_balance - $expired_points;
                }
            }

            if($expired_points>0) { 
                $point_transaction->expired_points = $expired_points . ' (ID# '.rtrim($expired_point_id).')';
            } else {
                $point_transaction->expired_points = 0;
            }

            $point_transaction->old_balance = $old_balance;
        }

        $user = User::find($id);
        $balance = $user->balance();
        $expiringnext = $user->nextExpiring();
        if($request->ajax())
        {
            return DataTables::of($data)
                    ->editColumn('data', function ($data) {
                        $messagedata = json_decode($data->data);
                        return [
                            'display'   => (!is_null($messagedata->message)) ?  e($messagedata->message) : 'N/A',
                        ];
                    })
                    ->editColumn('expiring_on', function ($data) {
                        return [
                            'display' => (!is_null($data->expiring_on)) ? $data->expiring_on->format('d M Y') : 'N/A',
                            'timestamp' => (!is_null($data->expiring_on)) ?  $data->expiring_on->timestamp : (10*9000*100000)
                        ];
                    })
                    ->editColumn('created_at', function ($data) {
                        return [
                            'display' => (!is_null($data->created_at)) ? $data->created_at->format('d M Y H:i:s') : 'N/A',
                            'timestamp' => (!is_null($data->created_at)) ?  $data->created_at->timestamp : (10*9000*100000)
                        ];
                    })
                    ->addColumn('balance', function($data) use ($data2) {

                        $entry = $data2->firstWhere('id', $data->id);
                        
                        $balance = $entry['old_balance'];
                        return ''.$balance;
                    })
                    ->addColumn('points_a', function($data) use ($data2) {

                        $entry = $data2->firstWhere('id', $data->id);

                        if($entry['transaction_type'] == 1) {
                            $balance = $entry['points_available'];
                            return ''.$balance;
                        } else {
                            return '';
                        }
                    })
                    ->addColumn('sales_user', function($data) use ($data2) {

                        $messagedata = json_decode($data->data);
                        return isset($messagedata->sub_heading) ?  $messagedata->sub_heading : '';
                    })
                    ->addColumn('expired_points', function($data) use ($data2) {

                        $entry = $data2->firstWhere('id', $data->id);
                        
                        $expired_points = $entry['expired_points'];
                        return ''.$expired_points;
                    })
                    ->rawColumns(['balance'])
                    ->make(true);
        }
        return view('admin.point-transaction.list')->with(['id'=>$id,'balance' => $balance,'expiringnext' => $expiringnext]);;
    }
    public function destroy($id)
    {
        $uid = PointTransaction::where('id',$id)->value('user_id');
        $pt = PointTransaction::find($id);
        $pt->delete();
        return redirect('/admin/point-transaction/'.$uid)->with('success','Record Deleted Successfully.');
    }
}
