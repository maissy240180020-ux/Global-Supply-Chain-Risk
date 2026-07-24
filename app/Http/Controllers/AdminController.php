<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Country;

class AdminController extends Controller
{
    // ==========================================================
    // DASHBOARD ADMIN
    // ==========================================================

    public function index()
    {
        $stats = [
            'total_users'   => User::count(),
            'total_admins'  => User::where('role', 'admin')->count(),
            'total_ports'   => DB::table('ports')->count(),
            'total_countries' => Country::count(),
            'high_risk'     => Country::where('risk_level', 'High')->count(),
        ];

        $recentUsers = User::latest()->take(5)->get();

        return view('admin.index', compact('stats', 'recentUsers'));
    }

    // ==========================================================
    // KELOLA USER
    // ==========================================================

    public function users()
    {
        $users = User::orderBy('created_at', 'desc')->get();
        return view('admin.users.index', compact('users'));
    }

    public function createUser()
    {
        return view('admin.users.create');
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'role'     => 'required|in:user,admin',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        return redirect()->route('admin.users')->with('success', 'User berhasil ditambahkan!');
    }

    public function editUser(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role'  => 'required|in:user,admin',
        ]);

        $data = [
            'name'  => $request->name,
            'email' => $request->email,
            'role'  => $request->role,
        ];

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:6|confirmed']);
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users')->with('success', 'User berhasil diperbarui!');
    }

    public function destroyUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak bisa menghapus akun sendiri!');
        }
        $user->delete();
        return redirect()->route('admin.users')->with('success', 'User berhasil dihapus!');
    }

    // ==========================================================
    // KELOLA DATASET PELABUHAN
    // ==========================================================

    public function ports()
    {
        $ports = DB::table('ports')
            ->orderBy('country_code')
            ->paginate(20)->fragment('ports-table');
        return view('admin.ports.index', compact('ports'));
    }

    public function createPort()
    {
        $countries = Country::orderBy('country_name')->get();
        return view('admin.ports.create', compact('countries'));
    }

    public function storePort(Request $request)
    {
        $request->validate([
            'port_name'    => 'required|string|max:255',
            'country_code' => 'required|string|max:5',
            'latitude'     => 'required|numeric|between:-90,90',
            'longitude'    => 'required|numeric|between:-180,180',
        ]);

        DB::table('ports')->insert([
            'port_name'    => $request->port_name,
            'country_code' => strtoupper($request->country_code),
            'latitude'     => $request->latitude,
            'longitude'    => $request->longitude,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        return redirect()->route('admin.ports')->with('success', 'Pelabuhan berhasil ditambahkan!');
    }

    public function editPort($id)
    {
        $port = DB::table('ports')->where('id', $id)->first();
        if (!$port) abort(404);
        $countries = Country::orderBy('country_name')->get();
        return view('admin.ports.edit', compact('port', 'countries'));
    }

    public function updatePort(Request $request, $id)
    {
        $request->validate([
            'port_name'    => 'required|string|max:255',
            'country_code' => 'required|string|max:5',
            'latitude'     => 'required|numeric|between:-90,90',
            'longitude'    => 'required|numeric|between:-180,180',
        ]);

        DB::table('ports')->where('id', $id)->update([
            'port_name'    => $request->port_name,
            'country_code' => strtoupper($request->country_code),
            'latitude'     => $request->latitude,
            'longitude'    => $request->longitude,
            'updated_at'   => now(),
        ]);

        return redirect()->route('admin.ports')->with('success', 'Data pelabuhan berhasil diperbarui!');
    }

    public function destroyPort($id)
    {
        DB::table('ports')->where('id', $id)->delete();
        return redirect()->route('admin.ports')->with('success', 'Pelabuhan berhasil dihapus!');
    }

    // ==========================================================
    // KELOLA ARTIKEL ANALISIS
    // ==========================================================

    public function articles()
    {
        // Artikel disimpan di sesi/session atau tabel custom
        // Kita gunakan tabel 'articles' jika ada, atau fallback ke array
        $articles = DB::table('articles')->orderByDesc('created_at')->paginate(15);
        return view('admin.articles.index', compact('articles'));
    }

    public function createArticle()
    {
        return view('admin.articles.create');
    }

    public function storeArticle(Request $request)
    {
        $request->validate([
            'title'    => 'required|string|max:255',
            'category' => 'required|string',
            'content'  => 'required|string',
            'author'   => 'required|string|max:100',
        ]);

        DB::table('articles')->insert([
            'title'      => $request->title,
            'category'   => $request->category,
            'content'    => $request->content,
            'author'     => $request->author,
            'sentiment'  => $request->sentiment ?? 'Neutral',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.articles')->with('success', 'Artikel berhasil dipublikasikan!');
    }

    public function editArticle($id)
    {
        $article = DB::table('articles')->where('id', $id)->first();
        if (!$article) abort(404);
        return view('admin.articles.edit', compact('article'));
    }

    public function updateArticle(Request $request, $id)
    {
        $request->validate([
            'title'    => 'required|string|max:255',
            'category' => 'required|string',
            'content'  => 'required|string',
            'author'   => 'required|string|max:100',
        ]);

        DB::table('articles')->where('id', $id)->update([
            'title'      => $request->title,
            'category'   => $request->category,
            'content'    => $request->content,
            'author'     => $request->author,
            'sentiment'  => $request->sentiment ?? 'Neutral',
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.articles')->with('success', 'Artikel berhasil diperbarui!');
    }

    public function destroyArticle($id)
    {
        DB::table('articles')->where('id', $id)->delete();
        return redirect()->route('admin.articles')->with('success', 'Artikel berhasil dihapus!');
    }
}