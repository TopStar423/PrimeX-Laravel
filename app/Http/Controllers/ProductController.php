<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Stock;

class ProductController extends Controller
{
    private function getProductsQuery($include_stock = false, $exclude_empty = false, $order_summary = false) {
        $query = Product::select('products.*');
        if ($include_stock) {
            $query = $query
                ->addSelect(DB::raw('SUM(stocks.on_hand) As on_hand_summary'))
                ->leftJoin("stocks", function($join) {
                    $join->on('products.id', '=', 'stocks.product_id');
                });

            if ($exclude_empty) {
                $query = $query->whereNotNull("stocks.product_id");
            }

            $query = $query->groupBy("products.id");

            if ($order_summary && in_array($order_summary, ['asc', 'desc'])) {
                $query = $query->orderBy("on_hand_summary", $order_summary);
            }
        }
        return $query;
    }

    public function index(Request $request)
    {
        $include_stocks = $request->query('include_stocks');
        $filter_empty = $request->query('filter_empty');
        $order_summary = $request->query('order_summary');

        $query = $this->getProductsQuery($include_stocks, $filter_empty, $order_summary);
        $products = $query->paginate(25);

        return response()->json($products);
    }

    public function create(Request $request)
    {
        $product = new Product;

        $product->code= $request->code;
        $product->name = $request->name;
        $product->description= $request->description;

        $product->save();

        return response()->json($product);
    }

    public function updateStock(Request $request, $id)
    {
        $validatedData = $this->validate($request, [
            'on_hand' => 'required|integer',
            'production_date' => 'required',
        ]);

        $stock = new Stock;

        $stock->product_id = $id;
        $stock->on_hand = $validatedData['on_hand'];
        $stock->production_date = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $validatedData['production_date'])));

        $stock->save();

        return response()->json($stock);
    }

    public function show(Request $request, $id)
    {
        $include_stocks = $request->query('include_stocks');
        $query = $this->getProductsQuery($include_stocks);

        $product = $query->where("products.id", "=", $id)->first();

        return response()->json($product);
    }

    public function update(Request $request, $id)
    {
        $product= Product::find($id);

        $product->code = $request->input('code');
        $product->name = $request->input('name');
        $product->description = $request->input('description');
        $product->save();
        return response()->json($product);
    }

    public function destroy($id)
    {
        $product = Product::find($id);
        $product->delete();

        return response()->json('product removed successfully');
    }
}
