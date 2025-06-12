@extends('admin.layouts.app')

@section('content')
    <header class="admin-header">
        <h1>👥 Користувачі</h1>
        <a href="{{ route('admin.customers.create') }}" class="btn-add">+ Додати</a>
    </header>

    <div class="table-wrapper">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Ім'я</th>
                    <th>Email</th>
                    <th>Роль</th>
                    <th>Дії</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($customers as $customer)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $customer->name }}</td>
                        <td>{{ $customer->email }}</td>
                        <td>
                            <span class="role-tag {{ $customer->is_admin ? 'admin' : 'user' }}">
                                {{ $customer->is_admin ? 'Адмін' : 'Користувач' }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('admin.customers.edit', $customer->user_id) }}" class="btn-edit">✏️</a>
                            <form action="{{ route('admin.customers.destroy', $customer->user_id) }}" method="POST"
                                class="delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-delete">🗑️</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
