<?php

namespace App\Http\Controllers\Manage;

use App\Account;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Validator;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        logger()->info(__METHOD__);

        // BEGIN
        $accounts = auth()->user()->accounts;

        return view('manage.accounts.index', compact('accounts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        logger()->info(__METHOD__);

        // BEGIN

        $account = new Account(); // For Form Model Binding

        return view('manage.accounts.create', compact('account'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        logger()->info(__METHOD__);

        // BEGIN

        $account = Account::firstOrNew($request->only('name'));

        auth()->user()->accounts()->save($account);

        logger()->info("Created accountId:{$account->id}");

        flash()->success(trans('manage.account.msg.store.success'));

        return redirect()->route('manage.accounts.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Account $account
     *
     * @return Response
     */
    public function edit(Account $account)
    {
        logger()->info(__METHOD__);
        logger()->info(sprintf('account:%s', $account->id));

        $this->authorize('manage', $account);

        // BEGIN

        return view('manage.accounts.edit', compact('account'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Account $account
     *
     * @return Response
     */
    public function update(Account $account, Request $request)
    {
        logger()->info(__METHOD__);
        logger()->info(sprintf('accountId:%s', $account->id));

        $this->authorize('manage', $account);

        // BEGIN

        $account->update($request->only([
            'name',
        ]));

        flash()->success(trans('manage.account.msg.update.success'));

        return redirect()->route('manage.accounts.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Account $account
     *
     * @return Response
     */
    public function destroy(Account $account)
    {
        logger()->info(__METHOD__);
        logger()->info(sprintf('accountId:%s', $account->id));

        $this->authorize('manage', $account);

        // BEGIN

        $account->forceDelete();

        flash()->success(trans('manage.account.msg.destroy.success'));

        return redirect()->route('manage.accounts.index');
    }

    /**
     * Allow the accout to another User.
     *
     * @param Account $account
     *
     * @return Response
     */
    public function allow(Account $account, Request $request)
    {
        logger()->info(__METHOD__);
        logger()->info(sprintf('accountId:%s', $account->id));

        $this->authorize('manage', $account);

        $validator = Validator::make($request->only(['email']), [
            'email' => 'bail|required|email',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                             ->withErrors($validator)
                             ->withInput();
        }

        // BEGIN

        $user = User::whereEmail($request->get('email'))->first();

        if (!$user) {
            flash()->error(trans('manage.account.msg.allow.user_not_found'));

            return redirect()->route('manage.accounts.index');
        }

        if (!$user->accounts->contains($account)) {
            $user->accounts()->save($account);
        }

        flash()->success(trans('manage.account.msg.allow.success'));

        return redirect()->route('manage.accounts.index');
    }
}
