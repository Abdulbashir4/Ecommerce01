<?php
namespace App\Livewire;
use Livewire\Component; use Livewire\WithPagination; use App\Models\Product;
class ProductSearch extends Component {use WithPagination; public string $search=''; public function updatingSearch(){$this->resetPage();} public function render(){return view('livewire.product-search',['products'=>Product::where('status',1)->when($this->search,fn($q)=>$q->where('product_name','like','%'.$this->search.'%'))->latest('product_id')->paginate(12)]);} }
