import Vue from 'vue'

Vue.filter('toUYU', function (value) {
  return `$${value}`;
})

Vue.filter('toUSD', function (value) {
  return `us$${value}`;
})

Vue.filter('toQuantity', function (value) {
  return `x${value}`;
})

Vue.filter('toWrap', function (value) {
  return `(${value})`;
})

Vue.filter('toTicket', function (value) {
  return `#${value}`;
})

Vue.filter('minus', function (value) {
  return `-$${value}`;
})

Vue.filter('plus', function (value) {
  return `+${value}`;
})