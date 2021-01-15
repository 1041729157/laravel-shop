/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

// 引入组件
require('./components/selectDistrict'); //地区选择组件
require('./components/UserAddressesCreateAndEdit');

/**
* 以下代码块可用于自动注册您的
* Vue组件。它将递归地扫描这个目录中的Vue
* 组件，并自动用它们的“basename”注册它们.
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))



/**
 * 接下来，我们将创建一个新的Vue应用程序实例并将其附加到
 * 这一页。然后，您可以开始向该应用程序添加组件
 * 或者定制JavaScript脚手架以满足您的独特需求
 */

const app = new Vue({
    el: '#app',
});
