<?php

namespace app\bra\controller;

class Index {

	public function index () {
		if (!file_exists(app()->getConfigPath() . 'install.lock')) {
			return redirect(url('install/index/index'));
		}
	}
}
