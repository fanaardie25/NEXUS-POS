<?php

use App\Models\Category;
use Livewire\Component;
use App\Models\Product;
use App\Models\Transaction;

new class extends Component
{
    public $search = '';
    public $activeCategory = 'all';
    public $cart = [];
    public $paymentMethod = 'cash';
    public $cashGiven = 0;
    public $total = 0;
    public $perPage = 12; // Jumlah awal produk

    public function loadMore()
    {
        $this->perPage += 12; 
    }
        
    public function scanProduct()
    {
        if (empty($this->search)) {
            return;
        }
        
        $product = Product::where('barcode', $this->search)
                         ->orWhere('sku', $this->search)
                         ->first();
        
        if ($product) {
            $this->addToCart($product->id);
            session()->flash('message', 'added ' . $product->name . ' to cart!');
        } else {
            
            session()->flash('error', 'product not found!');
        }
        
        
        $this->search = '';
    }

    public function addToCart($productId)
    {
        $product = Product::find($productId);
        
        if (!$product || $product->stock <= 0) return;

        if (isset($this->cart[$productId])) {
            if (($this->cart[$productId]['quantity'] + 1) > $product->stock) {
                $this->cart[$productId]['quantity'] = $product->stock;
                session()->flash('error', 'Stok terbatas, Bro! Cuma ada ' . $product->stock);
                return; 
            }
            
            
            $this->cart[$productId]['quantity']++;
        } else {
            $this->cart[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'image_path' => $product->image_path,
                'quantity' => 1
            ];
        }
    }

    public function updateQuantity($productId, $amount)
    {
        if (isset($this->cart[$productId])) {
            $this->cart[$productId]['quantity'] += $amount;
                $product = Product::find($productId);
            if ($this->cart[$productId]['quantity'] + 1 > $product->stock) {
                $this->cart[$productId]['quantity'] = $product->stock;
                session()->flash('error', "Insufficient stock. Only " . $product->stock . " items available");
            }
            if ($this->cart[$productId]['quantity'] <= 0) {
                unset($this->cart[$productId]);
            }
        }
    }

    public function clearAll() { $this->cart = []; }

    public function handleCheckout()
    {
    
        if (count($this->cart) <= 0) {
            session()->flash('error', 'Cart is empty!');
            return;
        }

        $transaction = Transaction::create([
            'invoice_number' => 'INV-' . strtoupper(uniqid()),
            'total' => $this->total,
            'payment_amount' => $this->paymentMethod == 'cash' ? $this->cashGiven : $this->total,
            'change_amount' => $this->paymentMethod == 'cash' ? $this->cashGiven - $this->total : 0,
            'payment_method' => $this->paymentMethod,
            'cashier_id' => auth()->id(),
        ]);
    
        foreach ($this->cart as $item) {
            if ($item['quantity'] <= 0) {
                session()->flash('error', 'Invalid quantity in cart!');
                return;
            }

            $transaction->items()->create([
                'product_id' => $item['id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'subtotal' => $item['price'] * $item['quantity'],
            ]);

        }

        foreach ($this->cart as $item) {
            $product = Product::find($item['id']);
            if ($product) {
                $product->stock -= $item['quantity'];
                $product->save();
            }
        }

        $this->clearAll();
        session()->flash('success', 'Checkout successful!');
         $this->redirect('/cashier'); 
    }

    public function render()
    {
        $query = Product::query();
        $category = Category::where('is_active', true)->get();

        if ($this->activeCategory !== 'all') {
            $query->where('category_id', $this->activeCategory);
        }

        if (!empty($this->search)) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        $products = $query->paginate($this->perPage)->withQueryString();

        
        $subtotal = collect($this->cart)->sum(fn($item) => $item['price'] * $item['quantity']);
        $tax = $subtotal * 0.1; 
        $this->total = $subtotal + $tax;

        return $this->view([
            'products' => $products,
            'cart' => $this->cart,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $this->total,
            'categories' => $category
        ])->layout('layouts.app');
    }
};
?>