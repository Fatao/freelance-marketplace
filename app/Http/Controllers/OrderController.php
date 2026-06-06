<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Category;
use App\Models\Skill;
use App\Models\OrderStatusHistory;
use App\Models\OrderWork;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['client.clientProfile', 'category', 'skills'])
            ->where('status', 'published');

        // Filters
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        if ($request->filled('keywords')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->keywords . '%')
                  ->orWhere('description', 'like', '%' . $request->keywords . '%');
            });
        }
        if ($request->filled('budget_min')) {
            $query->where('budget_max', '>=', $request->budget_min);
        }
        if ($request->filled('budget_max')) {
            $query->where('budget_min', '<=', $request->budget_max);
        }
        if ($request->filled('payment_format')) {
            $query->where('payment_format', $request->payment_format);
        }
        if ($request->filled('deadline_to')) {
            $query->whereDate('deadline', '<=', $request->deadline_to);
        }
        if ($request->filled('skills')) {
            $query->whereHas('skills', fn($q) => $q->whereIn('skills.id', $request->skills));
        }

        // Sort
        $sort = $request->get('sort', 'published_at');
        $dir  = $request->get('dir', 'desc');
        $allowedSorts = ['published_at', 'budget_min', 'budget_max', 'deadline'];
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $dir === 'asc' ? 'asc' : 'desc');
        }

        $orders     = $query->paginate(12)->withQueryString();
        $categories = Category::where('is_active', true)->get();
        $skills     = Skill::orderBy('name')->get();

        return view('orders.index', compact('orders', 'categories', 'skills'));
    }

    public function show(Order $order)
    {
        abort_if(!$order->isPublished() && auth()->id() !== $order->client_id, 403);

        $order->load(['client.clientProfile', 'category', 'skills', 'applications.freelancer']);
        $userApplication = null;

        if (auth()->check() && auth()->user()->isFreelancer()) {
            $userApplication = $order->applications()
                ->where('freelancer_id', auth()->id())
                ->first();
        }

        return view('orders.show', compact('order', 'userApplication'));
    }

    public function myOrders()
    {
        $orders = Order::where('client_id', Auth::id())
            ->withCount('applications')
            ->with('category')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('orders.my', compact('orders'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        $skills     = Skill::orderBy('name')->get();
        $order      = new Order();
        return view('orders.create', compact('categories', 'skills', 'order'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'required|string|min:50',
            'category_id'    => 'nullable|exists:categories,id',
            'budget_min'     => 'nullable|numeric|min:0',
            'budget_max'     => 'nullable|numeric|gte:budget_min',
            'payment_format' => 'required|in:fixed,hourly,negotiable',
            'deadline'       => 'nullable|date|after:today',
            'links'          => 'nullable|string',
            'skills'         => 'nullable|array',
            'skills.*'       => 'exists:skills,id',
        ]);

        $order = Order::create(array_merge($data, [
            'client_id' => Auth::id(),
            'status'    => 'draft',
        ]));

        if (!empty($data['skills'])) {
            $order->skills()->attach($data['skills']);
        }

        return redirect()->route('orders.my')
            ->with('success', 'Заказ создан как черновик.');
    }

    public function edit(Order $order)
    {
        abort_if($order->client_id !== Auth::id(), 403);
        abort_if(!in_array($order->status, ['draft', 'rejected']), 403);

        $categories = Category::where('is_active', true)->get();
        $skills     = Skill::orderBy('name')->get();

        return view('orders.edit', compact('order', 'categories', 'skills'));
    }

    public function update(Request $request, Order $order)
    {
        abort_if($order->client_id !== Auth::id(), 403);

        $data = $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'required|string|min:50',
            'category_id'    => 'nullable|exists:categories,id',
            'budget_min'     => 'nullable|numeric|min:0',
            'budget_max'     => 'nullable|numeric|gte:budget_min',
            'payment_format' => 'required|in:fixed,hourly,negotiable',
            'deadline'       => 'nullable|date|after:today',
            'links'          => 'nullable|string',
            'skills'         => 'nullable|array',
            'skills.*'       => 'exists:skills,id',
        ]);

        $order->update($data);
        $order->skills()->sync($data['skills'] ?? []);

        return redirect()->route('orders.my')->with('success', 'Заказ обновлён.');
    }

    public function destroy(Order $order)
    {
        abort_if($order->client_id !== Auth::id(), 403);
        abort_if(!in_array($order->status, ['draft', 'rejected']), 403);
        $order->delete();
        return redirect()->route('orders.my')->with('success', 'Заказ удалён.');
    }

    public function submitForModeration(Order $order)
    {
        abort_if($order->client_id !== Auth::id(), 403);
        abort_if(!in_array($order->status, ['draft', 'rejected']), 403);

        $old = $order->status;
        $order->update(['status' => 'on_moderation']);

        OrderStatusHistory::create([
            'order_id'   => $order->id,
            'changed_by' => Auth::id(),
            'old_status' => $old,
            'new_status' => 'on_moderation',
            'comment'    => 'Отправлен на модерацию',
        ]);

        return redirect()->route('orders.my')->with('success', 'Заказ отправлен на модерацию.');
    }

    public function cancel(Order $order)
    {
        abort_if($order->client_id !== Auth::id(), 403);

        $old = $order->status;
        $order->update(['status' => 'cancelled']);

        OrderStatusHistory::create([
            'order_id'   => $order->id,
            'changed_by' => Auth::id(),
            'old_status' => $old,
            'new_status' => 'cancelled',
        ]);

        return redirect()->route('orders.my')->with('success', 'Заказ отменён.');
    }

    public function updateWorkStatus(Request $request, Order $order)
    {
        $request->validate(['status' => 'required|in:in_progress,on_review,needs_revision,completed,cancelled']);

        $work = $order->work;
        abort_if(!$work, 404);

        $isClient     = Auth::id() === $order->client_id;
        $isFreelancer = Auth::id() === $work->freelancer_id;
        abort_if(!$isClient && !$isFreelancer, 403);

        $work->update(['status' => $request->status]);

        if ($request->status === 'completed') {
            $order->update(['status' => 'completed']);
            OrderStatusHistory::create([
                'order_id'   => $order->id,
                'changed_by' => Auth::id(),
                'old_status' => 'in_progress',
                'new_status' => 'completed',
            ]);
        }

        return back()->with('success', 'Статус обновлён.');
    }
}