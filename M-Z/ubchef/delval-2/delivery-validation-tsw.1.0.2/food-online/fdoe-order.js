jQuery(function(t) {
	var cat_ids;
	if (fdoe.is_prem == 1) {
		var cat_ids_raw = _.pluck(fdoe.cat_order, 'ID');
		cat_ids = cat_ids_raw.map(function(x) {
			return parseInt(x, 10);
		});
	}
	// Backbone model, collection and views
	if (
		Category = Backbone.Model.extend({
			defaults: {
				"updating": false
			},
			initialize: function() {
				this.set('id', this.get("cat_ID"));
			}
		}),
		Categories = Backbone.Collection.extend({
			parse: function(t) {
				return this.url = t.products
			},
			model: Category,
			filterByIds: function(idArray) {
				return this.reset(_.map(idArray, function(id) {
					return this.get(id);
				}, this));
			},
		}),
		Product = Backbone.Model.extend({
			defaults: {
				"updating": false
			}
		}),
		Products = Backbone.Collection.extend({
			parse: function(t) {
				return this.url = t.products
			},
			model: Product,
			firstAsCollection: function(numItems) {
				var models = this.first(numItems);
				return new Products(models);
			},
			restAsCollection: function(numItems) {
				var models = this.rest(numItems);
				return new Products(models);
			},
			filterById: function(idArray) {
				return this.reset(_.map(idArray, function(cat_id) {
					return this.get(cat_id);
				}, this));
			}
		}),
		Cat_Menu_Titles = Backbone.View.extend({
			tagName: "div",
			className: "menu_titles",
			initialize: function() {
				this.template = _.template(t("#categoryTemplate").html());
				if (fdoe.is_accordian == 1) {
					this.$el.attr("id", "acc_menucat_" + this.model.get("cat_ID"));
					this.$el.addClass('menu_titles_accord');
					if (fdoe.layout == 'fdoe_twentytwenty') {
						this.$el.addClass('aro-style-twenty-title');
					}
				}
			},
			render: function() {
				var t = _.extend(this.model.attributes, {});
				return this.$el.html(this.template(t)), this
			},
		}),
		CategoryView = Backbone.View.extend({
			tagName: "div",
			className: "cat_tbody scrollspy",
			isUpdating: !1,
			initialize: function() {

				this.$el.attr("id", "menucat_" + this.model.get("cat_ID"));
				this.$el.attr("role", "presentation");
				if (fdoe.is_accordian == 1) {
					this.$el.addClass('arocollapse');
				}
				if (fdoe.layout == 'fdoe_twentytwenty') {
					this.$el.addClass('aro-style-twenty');
				}
			},
			render: function() {
				var t = _.extend(this.model.attributes, {});
				return this.$el.html(), this
			},
		}),
		TopmenuView = Backbone.View.extend({
			tagName: "li",
			isUpdating: !1,
			initialize: function() {
				this.$el.attr("id", "headingtop_menucat_" + this.model.get("cat_ID"));
				this.template = _.template(t("#topmenuTemplate").html());
			},
			render: function() {
				var t = _.extend(this.model.attributes, {});
				if (fdoe.subcat_with_parent == 1) {
					if ((this.model.get('has_sub') === false && this.model.get('category_count') == 0) || this.model.get('category_parent') !== 0) {
						return this;
					}
				}
				return this.$el.html(this.template(t)), this;
			},
		}),
		SidemenuView = Backbone.View.extend({
			tagName: "div",
			className: 'fdoe_menuitem',
			isUpdating: !1,
			initialize: function() {
				this.$el.attr("id", "heading_menucat_" + this.model.get("cat_ID")),
					this.template = _.template(t("#sidemenuTemplate").html())
			},
			render: function() {
				var t = _.extend(this.model.attributes, {});
				if (fdoe.subcat_with_parent == 1) {
					if ((this.model.get('has_sub') === false && this.model.get('category_count') == 0) || this.model.get('category_parent') !== 0) {
						return this;
					}
				}
				return this.$el.html(this.template(t)), this
			},
		}),
		MainView = Backbone.View.extend({
			el: "#the_main_container",
			first: true,
			initialize: function() {
				var isIE11 = !!window.MSInputMethodContext && !!document.documentMode;
				if (fdoe.smooth_scrolling == 'no' || isIE11) {
					this.undelegateEvents();
				}
			},
			smooth_scrolling: function(e) {
				const supportsNativeSmoothScroll = 'scrollBehavior' in document.documentElement.style;
				if (supportsNativeSmoothScroll) {
					var check_header, y, iii, t_header, rr;
					e.preventDefault();
					check_header = t('header').css('position') == 'fixed' || t('.nav-container.fixed').css('position') == 'fixed' || (fdoe.sticky_bar == 'yes') ? false : true;
					if (check_header === true) {
						(t(e.currentTarget.getAttribute('href'))[0]).scrollIntoView({
							behavior: 'smooth',
							block: "start",
							inline: "nearest"
						});
					} else {
						t_header = t('header').css('position') == 'fixed' ? t('header').outerHeight(true) : 0;
						iii = !window.matchMedia('(max-width: 600px)').matches ? t('#wpadminbar').height() : 0;
						y = t('.top_small_affixed.aroaffix').outerHeight(true) + t('.fdoe-top-sticky.aroaffix').outerHeight(true);
						if (this.first === true && y === 0 && ((fdoe.sticky_bar == 'yes' && fdoe.sticky_mobile !== 'no' && window.matchMedia('(max-width: 767px)').matches) || (fdoe.sticky_bar == 'yes' && !window.matchMedia('(max-width: 767px)').matches))) {
							var extra = 0;
							if ((fdoe.sticky_bar == 'yes' && fdoe.sticky_mobile !== 'no' && window.matchMedia('(max-width: 767px)').matches)) {
								if (fdoe.sticky_mobile == 'cats') {
									extra = t('#menu_headings').outerHeight(true);
								} else if (fdoe.sticky_mobile == 'selector') {
									extra = t('#checkArray').outerHeight(true) + t('.top-bar_address_row').outerHeight(true);
								} else if (fdoe.sticky_mobile == 'both') {
									extra = t('#checkArray').outerHeight(true) + t('.top-bar_address_row').outerHeight(true) + t('#menu_headings').outerHeight(true);
								}
							} else {
								extra = t('#menu_headings').outerHeight(true);
							}
							y = 2 * extra;
						}
						rr = t(e.currentTarget.getAttribute('href')).offset().top - t_header - t('.nav-container.fixed').outerHeight(true) - y - iii;
						window.scrollTo({
							top: rr,
							behavior: 'smooth'
						});
					}
					this.first = false;
				}
			},
			smooth_reset: function() {
				this.first = true;
			},
			events: {
				'click  #menu_headings a[href^="#"]': "smooth_scrolling",
				'click  #menu_headings_2 a[href^="#"]': "smooth_scrolling",
				'aroaffixed-top.bs.aroaffix .top_small_affixed, .fdoe-top-sticky': 'smooth_reset',
			},
		}),
		CategoryListView = Backbone.View.extend({
			sortedproducts: null,
			counter: 0,
			container: null,
			el: ".fdoe-products",
			initialize: function() {
				_.bindAll(this, 'refresh_handler');
				// bind to window
				if (fdoe.lazy_load == 1) {
					this.$el.addClass('fdoe-lazy-loading');
					jQuery(window).scroll(this.refresh_handler);
					jQuery(window).load(this.refresh_handler);
					jQuery(window).resize(this.refresh_handler);
				}
			},
			render: function() {
				sortedproducts = new Products;
				this.container = document.createDocumentFragment();
				this.collection.categories.forEach(this.addOne, this);
				t('#the_menu').append(this.container);
				return sortedproducts;
			},
			reset: function() {
				this.$el.find("div.cat_tbody").remove(), this.render()
			},
			destroy_view: function() {
				this.undelegateEvents();
				this.$el.removeData().unbind();
				this.remove();
				Backbone.View.prototype.remove.call(this);
			},
			refresh_handler: function(e) {
				var elements = this.$(".fdoe_thumb");
				_.each(elements, function(element, i, list) {
					var boundingClientRect = elements[i].getBoundingClientRect();
					if (boundingClientRect.top < window.innerHeight + 500) {
						var id = jQuery(elements[i]).parents('.fdoe-item').data('pid');
						var amodel = this.collection.products.get(id);
						if (jQuery(elements[i]).find('img').length) {} else {
							jQuery(elements[i]).html(amodel.get('image').src);
						}
					}
				}, this);
			},
			addOne: function(e, i) {
				var m = this.collection.products.filter(function(b) {
					return _.indexOf(b.get("cat_id"), e.get("cat_ID"), false) !== -1;
				});
				if (fdoe.subcat_with_parent == 1) {
					if (e.get('has_sub') === false && (e.get('category_count') == 0 || m.length === 0)) {
						return;
					}
					if (e.get('category_parent') !== 0 && m.length !== 0) {
						this.add_sub(e, i);
						return;
					}
				} else {
					if (m.length === 0) {
						return;
					}
				}
				var o = new CategoryView({
					model: e
				});
				var title = new Cat_Menu_Titles({
					model: e
				});
				var titles = title.render().el;
				var container2 = o.render().el;
				if (this.counter === 0 && fdoe.is_accordian) {
					o.$el.addClass("in-aro");
					title.$('a').attr("aria-expanded", "true");
				}
				this.counter++;
				sortedproducts.add(m);
				m.forEach(function(e) {
					var p = new ProductView({
						model: e
					});
					container2.appendChild(p.render().el);
				}, container2);
				if (fdoe.layout == 'fdoe_twentytwenty') {
					title.$('.menu_titles_image').addClass('menu_titles_image_main').appendTo(container2);
					if (fdoe.is_accordian == 1 && (fdoe.top_bar_menu == 0 && fdoe.show_left_menu == 1)) {
						this.container.appendChild(titles);
					} else if (fdoe.is_accordian == 1) {
						o.$el.addClass('aro-style-twenty_2');
						container2.insertBefore(titles, container2.childNodes[0]);
					} else {
						container2.insertBefore(titles, container2.childNodes[0]);
					}
					this.container.appendChild(container2);
				} else {
					container2.insertBefore(titles, container2.childNodes[0]);
					this.container.appendChild(container2);
				}
			},
			add_sub: function(e, i) {
				var o = new CategoryView({
					model: e
				});
				var title = new Cat_Menu_Titles({
					model: e
				});
				var titles = title.render().el;
				var container2 = o.render().el;
				var m = this.collection.products.filter(function(b) {
					return _.indexOf(b.get("cat_id"), e.get("cat_ID"), false) !== -1;
				});
				sortedproducts.add(m);
				var parent_id = e.get('category_parent');
				m.forEach(function(e) {
					var p = new ProductView({
						model: e
					});
					container2.appendChild(p.render().el);
				}, container2);
				if (fdoe.layout == 'fdoe_twentytwenty') {
					title.$el.addClass('twenty-sub-title');
					title.$('.menu_titles_image').appendTo(container2);
					if (fdoe.is_accordian == 1 && (fdoe.top_bar_menu == 0 && fdoe.show_left_menu == 1)) {
						o.$el.addClass('aro-style-twenty_2 twenty-sub-cat twenty-sub-cat-accord').removeClass('arocollapse');
						container2.insertBefore(titles, container2.childNodes[0]);
					} else if (fdoe.is_accordian == 1) {
						o.$el.addClass('aro-style-twenty_2 twenty-sub-cat twenty-sub-cat-accord').removeClass('arocollapse');
						container2.insertBefore(titles, container2.childNodes[0]);
					} else {
						o.$el.addClass('aro-style-twenty_2 twenty-sub-cat').removeClass('arocollapse');
						container2.insertBefore(titles, container2.childNodes[0]);
					}
					var string = '#menucat_' + parent_id;
					var par_el_ = jQuery(this.container).find(string);

					if (par_el_ !== null) {
						par_el_.append(container2);
					}
				} else {
					if (fdoe.is_accordian == 1) {
						o.$el.removeClass('arocollapse');
					}
					container2.insertBefore(titles, container2.childNodes[0]);
					var string2 = '#menucat_' + parent_id;
					var par_el2 = jQuery(this.container).find(string2);
					if (par_el2 !== null) {
						par_el2.append(container2);
					}
				}
			},
		}),
		MenuView = Backbone.View.extend({
			container: null,
			container2: null,
			el: "#menu_headings",
			initialize: function() {},
			render: function() {
				container2 = document.createDocumentFragment();
				container = document.createDocumentFragment();
				this.collection.categories.forEach(this.addOne, this);
				t('#menu_headings').append(container);
				t('#menu_headings_2').append(container2);
			},
			reset: function() {},
			destroy_view: function() {
				// COMPLETELY UNBIND THE VIEW
				this.undelegateEvents();
				this.$el.removeData().unbind();
				// Remove view from DOM
				this.remove();
				Backbone.View.prototype.remove.call(this);
			},
			addOne: function(e, i) {
				var m = this.collection.products.filter(function(b) {
					return _.indexOf(b.get("cat_id"), e.get("cat_ID"), false) !== -1;
				});
				if (fdoe.subcat_with_parent == 1) {
					if (e.get('has_sub') === false && (e.get('category_count') == 0 || m.length === 0)) {
						return;
					}
					if (e.get('category_parent') !== 0 && m.length !== 0) {
						return;
					}
				} else {
					if (m.length === 0) {
						return;
					}
				}
				var topmenu = new TopmenuView({
					model: e
				});
				container.appendChild(topmenu.render().el);
				var sidemenu = new SidemenuView({
					model: e
				});
				container2.appendChild(sidemenu.render().el);
				if (i == 0 && fdoe.is_accordian) {
					topmenu.$('a').attr("aria-expanded", "true");
					sidemenu.$('a').attr("aria-expanded", "true");
				}
			},
		}),
		CartView = Backbone.View.extend({
			isUpdating: true,
			initialize: function() {
				$this = this;
				window.first = true;
				window.queue = [];
				_.bindAll(this, 'item_added_plus', 'item_added_minus');
				jQuery(document.body).off('wc_fragments_refreshed').on('wc_fragments_refreshed', function() {
					jQuery('.fdoe').removeClass('processing').unblock();
				});
			},
			events: {
				"click .fdoe_incre_button.fdoe_plus_button": "item_added_plus",
				"click .fdoe_incre_button.fdoe_minus_button": "item_added_minus",
			},
			blocking: function($current) {
				if (!jQuery('.fdoe').hasClass('processing')) {
					jQuery('.fdoe').addClass('processing').block({
						message: null,
						baseZ: 100000,
						overlayCSS: {
							background: '#fff',
							opacity: 0.01,
						}
					});
				}
				var ele = $current.parents('.fdoe_mini_cart').find('.fdoe_minicart_checkout_button');
				if (!ele.hasClass('processing')) {
					ele.addClass('processing').block({
						message: null,
						baseZ: 100000,
						overlayCSS: {
							background: '#fff',
							opacity: 0.6,
						}
					});
				}
				var ele2 = $current.parents('.fdoe_mini_cart_2').find('.fdoe_minicart_checkout_button');
				if (!ele2.hasClass('processing')) {
					ele2.addClass('processing').block({
						message: null,
						baseZ: 100000,
						overlayCSS: {
							background: '#fff',
							opacity: 0.6,
						}
					});
				}
			},
			item_added_plus: function(evt) {
				var me = this;
				var $current = this.$(evt.currentTarget).siblings(".quantity").find(".qty");
				var val = +($current.val());
				var max = parseInt($current.attr("max"));
				var step = 1;
				if (val === max) {
					jQuery('#stock_aromodal').aromodal('show');
					return false;
				}
				if (val + step > max) {
					jQuery.when($current.val(max)).done(function() {
						me.item_added($current);
					});
				} else {
					jQuery.when($current.val(val + step)).done(function() {
						me.item_added($current);
					});
				}
			},
			item_added_minus: function(evt) {
				var me = this;
				var $current = this.$(evt.currentTarget).siblings(".quantity").find(".qty");
				var val = +($current.val());
				var min = parseInt($current.attr("min"));
				var step = 1;
				if (val === min) {
					return false;
				}
				if (val - step < min) {
					jQuery.when($current.val(min)).done(function() {
						me.item_added($current);
					});
				} else {
					jQuery.when($current.val(val - step)).done(function() {
						me.item_added($current);
					});
				}
			},
			item_added: function($current) {
				var me = this;
				me.blocking($current);
				var value = +($current.val());
				if (value == 0) {
					$current.parents('.fdoe_minicart_item').fadeOut();
				}
				var hash = $current.attr('name').replace(/cart\[([\w]+)\]\[qty\]/g, "$1");
				if (isNaN(value)) {
					value = 0;
				}
				var $form = $current.closest("form");
				window.queue.forEach(function(e, i) {
					if (jQuery.inArray(hash, e) !== -1) {
						window.queue.splice(i, 1);
					}
				});
				window.queue.push([value, hash]);
				if (window.first === true) {
					window.first = false;
					me.moveAlong();
				}
			},
			moveAlong: function() {
				var request;
				if (window.queue.length) {
					request = queue.shift();
					clearTimeout(this.timeout);
					var $this = this;
					this.timeout = setTimeout(function() {
						$this.updateQuantity(request);
					}, 500);
				} else {
					jQuery.ajax(jQueryfdoe_fragment_refresh);
					window.first = true;
				}
			},
			updateQuantity: function(item) {
				var u = this;
				var value = item[0];
				var hash = item[1];
				isNaN(value) && (value = 0);
				if (hash) {
					data = {
						hash: hash,
						quantity: value,
						update_cart: true,
					};
					t.ajax({
						url: fdoe.wc_ajax_url.replace('%%endpoint%%', 'ajaxfdoe_qty_cart'),
						type: "POST",
						data: data,
						beforeSend: function() {}
					}).success(function() {}).done(function(response) {
						if (response.is_sold_indi == true && response.try_qty != 0) {
							jQuery('#stock_indi_aromodal').aromodal('show');
						}
						u.moveAlong();
					});
				}
			},
		}),
		ProductView = Backbone.View.extend({
			tagName: "div",
			className: "fdoe-item",
			events: {
				"keyup .fdoe_add_item.fdoe-simple": "add_simple",
				"click input[type='radio']": "change_url",
				"click .fdoe_add_item.fdoe-variable": "add_var",
			},
			isUpdating: !1,
			initialize: function() {
				// _.bindAll(this, 'add_simple', 'render');
				this.$el.attr("data-pid", this.model.get("id"));
				if ((fdoe.popup_variable == "yes" && this.model.get("is_variable")) || (fdoe.popup_simple == "yes" && this.model.get("is_simple"))) {
					this.$el.attr("role", "button"), this.$el.attr("data-toggle", "aromodal"), this.$el.attr("data-target", "#myModal_" + this.model.get("parent_id"))
				} else {
					this.$el.addClass('fdoe_is_button')
				}
				this.$el.addClass('fdoe-border-' + fdoe.fdoe_item_separator), this.$el.addClass(fdoe.layout), this.$el.attr("id", "fdoe_item_" + this.model.get("id") + _.random(0, 1000)),
					this.template = _.template(t("#productTemplate").html()), this
			},
			render: function() {
				var t = _.extend(this.model.attributes, {});
				return this.$el.html(this.template(t)), this
			},
			change_url: function(e) {
				var variation_id = t(e.currentTarget).data('variation_id');
				var p_id = t(e.currentTarget).data('p_id');
				t(e.currentTarget).parents('.fdoe-vari-form').find('input[name="p_id"]').val(p_id);
				t(e.currentTarget).parents('.fdoe-vari-form').find('input[name="variation_id"]').val(variation_id);
				t(e.currentTarget).parents('.fdoe-item').find('.fdoe_var_add').data('inactive', false);
			},
			add_var: function(e) {
				var $$this = t(e.currentTarget).find('.fdoe_var_add');
				if (t(e.currentTarget).find('.fdoe-product-link').length) {} else {
					e.preventDefault();
				}
				if (!$$this.parents('.fdoe-item').hasClass('fdoe_is_button')) {
					return;
				}
				if ($$this.data('inactive') === true) {
					alert(fdoe.make_a_selection);
					return;
				}
				jQuery.blockUI({
					baseZ: 100000,
					message: "",
					overlayCSS: {
						backgroundColor: '#ffffff0d',
						opacity: 1
					},
				});
				// Ajax add to cart on the product page
				var jQueryform = $$this.parents('.fdoe-item').find('form.fdoe-vari-form');
				jQuery.ajax({
					url: fdoe.wc_ajax_url.replace('%%endpoint%%', 'ajaxfdoe_add'),
					method: "POST",
					data: jQueryform.serialize()
				}).success(function() {
					jQuery.ajax(jQueryfdoe_fragment_refresh);
				}).done(function(response) {
					jQueryform[0].reset();
					$$this.data('inactive', true);
					if (response.passed_vali !== false) {
						if (fdoe.show_conf == 'yes') {
							$$this.parent('span').siblings('.fdoe_confirm_check').fadeIn().delay(2000).fadeOut();
						}
					} else if (response.status == 'addon_error') {
						alert(fdoe.make_a_selection);
					}
					if (response.is_sold_indi == true) {
						jQuery('#stock_indi_aromodal').aromodal('show');
					}
					if (response.overstock == true) {
						jQuery('#stock_aromodal').aromodal('show');
					}
					jQuery.unblockUI();
				});
			},
			add_simple: function(e) {
				e.preventDefault(); 
				var $$this = jQuery(e.currentTarget).find('a.fdoe_simple_add_to_cart_button');
				if (!$$this.parents('.fdoe-item').hasClass('fdoe_is_button')) {
					return;
				}
				jQuery.blockUI({
					baseZ: 100000,
					message: null,
					overlayCSS: {
						backgroundColor: '#ffffff0d',
						opacity: 0.6
					},
					css: {
						padding: 0,
						margin: 0,
						width: '30%',
						top: '40%',
						left: '35%',
						textAlign: 'center',
						backgroundColor: '#fff',
						cursor: 'wait'
					},
				});
				var p_id = $$this.data('product_id');
				var q = $$this.data('quantity'); 
				var data = {
					p_id: p_id,
					quantity: q
				};
				jQuery.ajax({
					url: fdoe.wc_ajax_url.replace('%%endpoint%%', 'ajaxfdoe_add'),
					method: "POST",
					data: data
				}).success(function() {
					jQuery.ajax(jQueryfdoe_fragment_refresh);
				}).done(function(response) {
					if (response.passed_vali !== false) {
						if (fdoe.show_conf == 'yes') {
$$this.parent('span').siblings('.fdoe_confirm_check').fadeIn().delay(2000).fadeOut();
						} 
					} else if (response.status == 'addon_error') {
						alert(fdoe.make_a_selection);
					}
					if (response.is_sold_indi == true) {
						jQuery('#stock_indi_aromodal').aromodal('show');
					}
					if (response.overstock == true) {
						jQuery(".aromodal.product-aromodal").aromodal("hide");
						jQuery('#stock_aromodal').aromodal('show');
					}
					jQuery.unblockUI();
				});
			}
		}),
		ProductViewModal = Backbone.View.extend({
			tagName: "div",
			className: "fdoe-modal-wrapper",
			isUpdating: !1,
			events: {
				//"shown.bs.aromodal  .product-aromodal": "change_addon_price",
				//"shown.bs.aromodal   .product-aromodal": "on_shown",
				"show.bs.aromodal  .product-aromodal": "on_show",
				"hidden.bs.aromodal  .product-aromodal": "on_hidden",
				"click  .aromodal .single_add_to_cart_button": "on_click",
				"click  .aromodal .cart-button": "on_click",
			},
			initialize: function() {
				_.bindAll(this, 'on_show', 'on_click');
				this.template = fdoe.product_modal_template == 1 ? _.template(t("#productmodalTemplate2").html()) : _.template(t("#productmodalTemplate").html());

			},
			render: function() {
				var t = _.extend(this.model.attributes, {});
				return this.$el.html(this.template(t)), this

			},
			on_click: function(e) {
				var tthis = t(e.currentTarget);
				if (jQuery(e.currentTarget).hasClass('wc-variation-selection-needed')) {
					return;
				}
				if (jQuery(e.currentTarget).hasClass('disabled')) {
					return;
				}
				if (jQuery(e.currentTarget).parents('.product-aromodal').find('.wc-pao-required-addon').length !== 0) {
					//return;
				}
				e.preventDefault();
				jQuery.blockUI({
					baseZ: 100000,
					message: "",
					overlayCSS: {
						backgroundColor: '#ffffff0d',
						opacity: 1
					},
				});
				// Ajax add to cart on the product page
				var jQueryform = jQuery(e.currentTarget).closest('form');
				var formdata = jQueryform.serializeArray();
				var variations = {};
				_.each(formdata, function(el, i) {
					if (el.name === "add-to-cart") {
						el.name = "p_id";
					}
					if ((el.name).indexOf('attribute_') > -1) {
						variations[el.name] = el.value;
					}
				});
				formdata.push({
					'name': 'variation_atts',
					'value': JSON.stringify(variations)
				});
				jQuery.ajax({
					url: fdoe.wc_ajax_url.replace('%%endpoint%%', 'ajaxfdoe_add'),
					method: "POST",
					data: formdata
				}).success(function() {
					jQuery.ajax(jQueryfdoe_fragment_refresh);
				}).done(function(response) {
					if (response.is_sold_indi == true) {
						jQuery(".aromodal.product-aromodal").aromodal("hide");
						jQuery('#stock_indi_aromodal').aromodal('show');
					} else if (response.overstock == true) {
						jQuery(".aromodal.product-aromodal").aromodal("hide");
						jQuery('#stock_aromodal').aromodal('show');
					} else if (response.passed_vali !== false) {
						jQuery(".aromodal.product-aromodal").aromodal("hide");
						if (fdoe.show_conf == 'yes') {
							var data_id_fdoe = tthis.closest('.product-aromodal').data('id');
							jQuery('[data-target="#myModal_' + data_id_fdoe + '"]').find('.fdoe-alert').fadeIn(400).delay(2500).fadeOut(400);
						}
					} else {
						if (response.status == 'addon_error') {
							var message_2 = fdoe.addon_required;
							alert(message_2);
						}
					}
					jQuery.unblockUI();
				});
			},
			on_hidden: function(e) {
				if (fdoe.product_modal_template == 1 && fdoe.fallback_popup == 0 ) {
					try {
						if (this.model.get('id') !== undefined) {
							var tthis = t(e.currentTarget);
							tthis.find('.fdoe-modal-2-add').html('');
						}
					} catch (err) {}
				}
			},
			change_addon_price: function(elem) {
				elem.block({
					message: null,
					baseZ: 100000,
					overlayCSS: {
						background: '#fff',
						opacity: 0.6,
					}
				});
				var price = elem.find('.woocommerce-variation-price').find('.woocommerce-Price-amount.amount').text();
				var price2 = (Number(price.replace(/[^\d.-]/g, '')));
				var form_rows = elem.find('.form-row');
				form_rows.each(function(index) {
					var per = t(this).find('input[data-price-type="percentage_based"]').data('price');
					if (typeof per !== 'number') {
						return;
					}
					var label = t(this).find('input[data-price-type="percentage_based"]').data('label');
					var labelel = t(this).find('input[data-price-type="percentage_based"]').parent('label');
					var input = t(this).find('input[data-price-type="percentage_based"]');
					var data = per * price2 / 100;
					jQuery.ajax({
						url: fdoe.wc_ajax_url.replace('%%endpoint%%', 'ajaxfdoe_get_wc_price_2'),
						method: "POST",
						data: {
							price: data
						}
					}).success(function(response) {
						input.detach();
						labelel.html(label + ' ' + response.price);

						labelel.insertBefore(input, labelel.childNodes[0]);
						if (form_rows.length - 1 == index) {
							elem.unblock();
						}
					});
				});
			},
			on_shown: function(e) {
				var elem = t(e.currentTarget).find('.variations_form:not(.in_loop )');
				var r = this;
				elem.on('show_variation', function(event, variation) {
					r.change_addon_price(t(this));
				});
				elem.on('hide_variation', function(event, variation) {
					alert('hide_variation');
				});
			},
			on_show: function(e) {
				try {
					var tthis = t(e.currentTarget);
					if (fdoe.product_modal_template_ == 'style-1') {
						if (this.model.get('id') !== undefined) {
							if(tthis.find('.fdoe-modal-2-image').children().length == 0){
							tthis.find('.fdoe-modal-2-image').html(this.model.get("image").src);
							}
							if(fdoe.fallback_popup == 0){

							tthis.find('.fdoe-modal-2-add').html(this.model.get("single_shortcode"));


							}
							//for compatibility with YITH Points & Rewards
							if (fdoe.yith_points == 1) {
								t.fn.yith_ywpar_variations();
							}
						}
					} else if (fdoe.product_modal_template_ == 'custom') {
						if (this.model.get('id') !== undefined) {
							if(fdoe.fallback_popup == 0){
							tthis.find('.fdoe_insert_product_shortcode').html(this.model.get("single_shortcode"));


							}
						}
					}
				if(fdoe.fallback_popup == 0){
					tthis.find(('.variations_form')).wc_variation_form();
				}

				} catch (err) {}

			jQuery(e.currentTarget).find('form.cart').find('.reset_variations').click();
				jQuery(e.currentTarget).find('form.cart').trigger('woocommerce-product-addons-update');
				jQuery(e.currentTarget).find('.wc-tabs-wrapper, .woocommerce-tabs, #rating').trigger('init');
			},
		}),
		FeaturedViewModal = Backbone.View.extend({
			el: ".extra_aromodal",
			isUpdating: !1,
			events: {
				"click .single_add_to_cart_button": "on_click",
			},
			initialize: function() {
				_.bindAll(this, 'on_click');
			},
			on_click: function(e) {
				var tthis = t(e.currentTarget);
				e.preventDefault();
				jQuery.blockUI({
					baseZ: 100000,
					message: "",
					overlayCSS: {
						backgroundColor: '#ffffff0d',
						opacity: 1
					},
				});
				// Ajax add to cart from featured extra modal
				var jQueryform = jQuery(e.currentTarget).closest('form');
				var formdata = jQueryform.serializeArray();
				_.each(formdata, function(el, i) {
					if (el.name === "add-to-cart") el.name = "p_id";
				});
				jQuery.ajax({
					url: fdoe.wc_ajax_url.replace('%%endpoint%%', 'ajaxfdoe_add'),
					method: "POST",
					data: formdata
				}).success(function() {
					jQuery.ajax(jQueryfdoe_fragment_refresh);
				}).done(function(response) {
					if (response.passed_vali !== false) {
						tthis.siblings('.fdoe_confirm_check').fadeIn();
					} else {
						if (response.status == 'addon_error') {
							tthis.siblings('.fdoe_confirm_check').after('<span class="fdoe-temp-error">error</span>');
							setTimeout(function() {
								t('.fdoe-temp-error').fadeIn().remove();
							}, 5000);
						}
					}
					if (response.is_sold_indi == true) {
						window.alert(fdoe.can_not_add_message);
					} else if (response.overstock == true) {
						window.alert(fdoe.can_not_add_message);
					}
					jQuery.unblockUI();
				});
			},
		}),
		ProductListViewModal = Backbone.View.extend({
			el: "#fdoe-product-modals-inner",
			initialize: function() {},
			render: function() {
				this.$el.empty();
				var container = document.createDocumentFragment();
				this.collection.forEach(function(e) {
					var o = new ProductViewModal({
						model: e
					});
					container.appendChild(o.render().el);
				}, this);
				t('#fdoe-product-modals').append(container);

			},
			reset: function() {
				this.$el.find("div.fdoe-modal-wrap-test").remove(), this.render()
			},
			destroy_view: function() {
				// COMPLETELY UNBIND THE VIEW
				this.undelegateEvents();
				this.$el.removeData().unbind();
				// Remove view from DOM
				this.remove();
				Backbone.View.prototype.remove.call(this);
			},
			addOne: function(e) {
				var o = new ProductViewModal({
					model: e
				});
				t('#fdoe-product-modals').append(o.render().el);
			}
		}),
		"undefined" !== typeof Food_Online_Items && Food_Online_Items !== null && fdoe.js_frontend == 1) {
		t(".woocommerce-pagination").hide();
		new MainView();
		var i = new Products();
		var u = new Categories();
		u.add(fdoe.cats);

		if (fdoe.subcat_with_parent == 1) {
			var non_sub_cats_ = u.filter({
				category_parent: 0
			});
			var non_sub_cats = new Categories(non_sub_cats_);
			var sub_cats_ = u.reject({
				category_parent: 0
			});
			non_sub_cats.add(sub_cats_);
			uu = non_sub_cats;
		} else {
			var uuu = u.reject({
				category_count_not_children: 0
			});
			var uu = new Categories(uuu);
		}
		u.reset();

		i.add(Food_Online_Items.products);
		if (i.length > 50 && fdoe.is_prem == 0) {
			alert(fdoe.free_limit);
			i.pop();
		}
		var q;
		if (fdoe.is_prem == 1 && typeof fdoe_short === 'undefined') {
			var m = uu.filterByIds(cat_ids);
			q = new Categories(m);
		} else if ((typeof fdoe_short !== 'undefined') && (typeof fdoe_short.cats !== 'undefined') && fdoe_short.cats.length !== 0) {
			var h = uu.filterByIds(fdoe_short.cats);
			q = new Categories(h);
		} else {
			q = uu;
		}
		var new_cats = q.filter(function(e) {
			return e.has('cat_ID');
		});
		q = new Categories(new_cats);
		var y = new CategoryListView({
			collection: {
				products: i,
				categories: q
			}
		});
		var top = new MenuView({
			collection: {
				products: i,
				categories: q
			}
		});
		top.render();
		var r = y.render();
		var ii = r.reject({
			single_shortcode: ''
		});
		var iii = new Products(ii);
		if ((fdoe.popup_simple == "yes")) {
			var simple = iii.where({
				is_simple: true
			});
			var x = new ProductListViewModal({
				collection: simple
			});
			x.render();
		}
		if ((fdoe.popup_variable == "yes")) {
			var vari = iii.where({
				is_variable: true
			});
			z = new ProductListViewModal({
				collection: vari
			});
			z.render();

		}
		if (typeof fdoedel !== 'undefined' && fdoedel.is_featured_products) {
			new FeaturedViewModal();
		}
		jQuery('.fdoe').fadeIn(400).promise().then(function() {
			addmodals();
		}).done(function() {

			if (fdoe.minicart_style == 'increment') {
				window.no_cart_view = false;
				if (fdoe.hide_minicart != 'yes') {
					new CartView({
						el: '#fdoe_mini_cart_id'
					});
				}
				new CartView({
					el: '#fdoe_mini_cart_id_2'
				});
			} else {
				window.no_cart_view = true;
			}
		});
		jQuery('#menu_headings_2').fadeIn(400);
	}
	if (fdoe.is_checkout == 1) {
		if (fdoe.show_error_messages == 1) {
			jQuery('ul.woocommerce-error').css('display', 'block');
			t(document.body).on('checkout_error', function() {
				jQuery('ul.woocommerce-error').css('display', 'block');
			});
		}
	} else if (fdoe.js_frontend == 1) {
		if ( false && fdoe.fallback_popup == 1 && typeof wc_add_to_cart_variation_params !== 'undefined' ) {
			t( '.variations_form' ).each( function() {
				t( this ).wc_variation_form();
			});
		}
		var jQueryfdoe_fragment_refresh = {
			url: wc_cart_fragments_params.wc_ajax_url.toString().replace('%%endpoint%%', 'get_refreshed_fragments'),
			type: 'POST',
			beforeSend: function() {},
			success: function(data) {
				if (data && data.fragments) {
					if (window.no_cart_view === true || (window.first && ((window.fdoe_counter <= 1 && window.fdoe_counter_active === true) || window.fdoe_counter_active === false))) {
						jQuery.each(data.fragments, function(key, value) {
							jQuery(key).replaceWith(value);
						});
						jQuery('.fdoe_mini_cart').html(data.fragments['div.widget_shopping_cart_content']).show(function() {});
						jQuery('.fdoe_mini_cart_2').html(data.fragments['div.widget_shopping_cart_content']).show(function() {});
						jQuery(document.body).trigger('wc_fragments_refreshed');
						if (fdoe.minicart_style != 'theme') {
							init_minicart();
						}
					}
				}
			},
			complete: function() {
				if (window.fdoe_counter_active === true) {
					if (window.fdoe_counter <= 1) {
						window.fdoe_counter_active = false;
					}
					window.fdoe_counter--;
				}
			}
		};
		run_sequenze(do_style, do_sticky_bars, extras, add_event_listeners, init_minicart);
	}

	function addmodals() {
		var v = r.where({
			single_shortcode: ''
		});
		var rest = new Products(v);
		var simple_2;
		var vari_2;
		var the_ids;
		var the_ids2;
		if ((fdoe.popup_simple == "yes")) {
			simple_2 = rest.where({
				is_simple: true
			});
			the_ids = _.pluck(simple_2, 'id');
		}
		if ((fdoe.popup_variable == "yes")) {
			vari_2 = rest.where({
				is_variable: true
			});
			the_ids2 = _.pluck(vari_2, 'id');
		}
		var union2 = _.union(the_ids2, the_ids);
		if (union2.length !== 0) {
			var ceil = Math.ceil;
			Object.defineProperty(Array.prototype, 'chunk_fdoe', {
				value: function(n) {
					var this_ = this;
					return Array(ceil(this.length / n)).fill().map(function(_, i) {
						return this_.slice(i * n, i * n + n);
					});
				}
			});
			var sample = union2.chunk_fdoe(50);
			window.sample = sample;
			do_request();
		} else {}
	}

	function do_request() {
		var s = window.sample.shift();
		fdoe_inject_shortcode(s);
	}

	function fdoe_inject_shortcode(sample) {
		var request = {
			url: fdoe.wc_ajax_url.replace('%%endpoint%%', 'ajaxfdoe_make_product_shortcode'),
			method: "POST",
			data: {
				'id': sample
			},
			success: function(response) {
				var new_modals = new Products;
				new_modals.add(response.content);
				var p = new ProductListViewModal({
					collection: new_modals
				});
				p.render();
			},
			complete: function() {
				if (window.sample.length) {
					do_request();
				}
				extras();
			},
			error: function(data) {}
		};
		jQuery.ajax(request);
	}

	function run_sequenze(cb_do_style, cb_do_sticky_bars, cb_extras, cb_add_event_listeners, cb_init_minicart) {
		cb_do_style();
		cb_do_sticky_bars(activate_scroll);
		cb_extras();
		cb_add_event_listeners();
		if (fdoe.minicart_style != 'theme') {
			cb_init_minicart();
		}
	}

	function init_minicart() {
		//Mini Cart
		window.fdoe_counter = 0;
		window.fdoe_counter_active = false;
		var is_touch_device = 'ontouchstart' in document.documentElement;
		if (!is_touch_device && fdoe.minicart_style == "popover") {
			// Minicart remove button aropopover
			t(document).aropopover({
				selector: '.woocommerce-mini-cart-item',
			});
			t('.fdoe_minicart_item[data-toggle="aropopover"]').aropopover({
				delay: {
					show: 50,
					hide: 1800
				}
			}, {
				template: '<div class="aropopover" role="tooltip"><div class="arrow"></div><div class="aropopover-content fdoe_remove_aropopover"></div></div>'
			});
		} else if (is_touch_device || fdoe.minicart_style !== "popover") {
			// Minicart remove button aropopover
			t('.fdoe-mini-cart-remove').show();
			t('.fdoe-minicart-main-column').removeClass('arocol-xs-10').addClass('arocol-xs-12');
			t('#fdoe_mini_cart_id').addClass('minicart_items_basic');
			t('.fdoe_mini_cart_2 [data-toggle="aropopover"]').aropopover('destroy');
			t('.fdoe_mini_cart [data-toggle="aropopover"]').aropopover('destroy');
		}
	}

	function update_layout() {
		do_sticky_bars(activate_scroll);
		if (((window.matchMedia('(max-width: 767px)').matches) && fdoe.top_bar_mobile == 1) || ((!window.matchMedia('(max-width: 767px)').matches && fdoe.top_bar_menu == 1))) {
			var parentwidth33 = t("#fdoe-right-container").width();
			t(".fdoe-right-sticky").width(parentwidth33);
			jQuery('#menu_headings').css('display', 'flex');
			jQuery('#fdoe_products_id').css('width', 'unset');
		} else {
			jQuery('#menu_headings').css('display', 'none');
		}
		do_style();
		do_sticky_bars(activate_scroll);
		var isIE11 = !!window.MSInputMethodContext && !!document.documentMode;
	}

	function add_responsivness() {
		window.setTimeout(function() {
			jQuery(window).off('resize.fdoe').on('resize.fdoe', _.debounce(update_layout, 1000));
		}, 4000);
		jQuery(window).off("orientationchange.fdoe").on("orientationchange.fdoe", _.debounce(update_layout, 300));
	}

	function add_event_listeners() {
		add_responsivness();
		jQuery(document).on('submit', '.entry-summary form.cart', function(event) {
			event.preventDefault();
		});
		//hide product aromodal after added to cart
		jQuery('#cart_aromodal').on('show.bs.aromodal', function() {
			jQuery(".aromodal.product-aromodal").aromodal("hide");
		});
		// Hide cart aromodal on checkout
		jQuery(document).on('click', '#checkout_button_1', function() {
			jQuery("#cart_aromodal").aromodal('hide');
		});
		// Update mini cart on item removal
		jQuery(document.body).on('removed_from_cart', function(event) {
			event.preventDefault();
			jQuery.ajax(jQueryfdoe_fragment_refresh);
		});
		jQuery(document.body).on('wc_fragments_refreshed', function() {
			jQuery.unblockUI();
		});
		// Toggle class on clicked remove button
		jQuery(document).on('click', '.fdoe-mini-cart-remove a.fdoe_remove', function() {
			jQuery(this).parents('.fdoe_minicart_item').addClass('processing').block({
				message: null,
				baseZ: 100000,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
		});
		jQuery(document).on('click', '.aropopover-content a.fdoe_remove', function() {
			jQuery(this).parents('.aropopover').addClass('fdoe_clicked');
			jQuery(this).parents('.aropopover').prev('.fdoe_minicart_item').addClass('processing').block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
		});
		// Readjust aromodal
		jQuery(document).on('shown.bs.aromodal', '.fdoe-aromodal', function() {
			jQuery(this).aromodal('handleUpdate');
		});
	}

	function extras() {
		//Styling
		jQuery('.product-modal-style-1 button.modal-close').css("color", fdoe.menu_color);
		//for woocommerce-product-addons above version 3.0.0
		if (fdoe.addonabove3 == '1') {
			jQuery('li.fdoe_minicart_item.woocommerce-mini-cart-item').css('flex-direction', 'column');
			jQuery('.product-aromodal').off('show.bs.aromodal.fdoe').on('show.bs.aromodal.fdoe', function() {
				jQuery(this).find('form.cart').trigger('woocommerce-product-addons-update');
				if (jQuery('.wc-bookings-booking-form', this).length === false || true) {
					var qty_2 = parseFloat(jQuery('.cart', this).find('input.qty').val());
					var productname_new = t('.product_title', this).html();
					jQuery('.wc-pao-col1', this).first().html('<strong>' + qty_2 + 'x ' + productname_new + '</strong>');
					tthis = jQuery(this);
					jQuery('body').off('woocommerce-product-addons-update.fdoe').on('woocommerce-product-addons-update.fdoe', function() {
						var qty_2 = parseFloat(jQuery('.cart', tthis).find('input.qty').val());
						var productname_new = jQuery('.product_title', tthis).html();
						jQuery('.wc-pao-col1', tthis).first().html('<strong>' + qty_2 + 'x ' + productname_new + '</strong>');
					});
				}
			});
		}
		//for compatibility with YITH Points & Rewards
		if (fdoe.yith_points == 1) {
			t.fn.yith_ywpar_variations();
		}
	}
	// Sticky right and left-left containers
	function do_sticky_bars(callback_activate_scroll) {
		var right_aroaffix_top = 20;
		if (fdoe.sticky_bar == 'yes') {
			if (!window.matchMedia('(min-width: 768px)').matches && fdoe.sticky_mobile != 'no') {
				init_event_list_mobile();
			} else {
				t('#menu_headings').detach().removeClass('menu_headings_fixed').appendTo('#fdoe_products_id');
				t('.fdoe-right-sticky').removeClass('top_small_affixed');
			}
			var mode_is_big_screen = (window.matchMedia('(min-width: 768px)').matches && fdoe.hide_minicart == 'no') ? true : false;
			if (mode_is_big_screen || (!window.matchMedia('(min-width: 768px)').matches && fdoe.sticky_mobile != 'no')) {
				t(".fdoe-right-sticky").css('top', get_sticky_top(right_aroaffix_top) + 'px');
				t(".fdoe-right-sticky").aroaffix({
					offset: {
						bottom: function() {
							return (this.bottom = t(document).height() - t('#fdoe-left-container').offset().top - t('#fdoe-left-container').outerHeight(true))
						},
						top: function() {
							var top = fdoe.top_bar == 0 || mode_is_big_screen ? t('.fdoe-right-sticky').offset().top : t('.fdoe-top-bar-header').offset().top;
							return (this.top = top)
						}
					}
				});
				do_scrolling(right_aroaffix_top);
				var parentwidth = t("#fdoe-right-container").width();
				t(".fdoe-right-sticky").width(parentwidth);
			}
			// re-init event listeners
			if (!window.matchMedia('(min-width: 768px)').matches && fdoe.sticky_mobile != 'no') {
				init_event_list_mobile();
			} else {
				t('#menu_headings').detach().removeClass('menu_headings_fixed').appendTo('#fdoe_products_id');
				t('.fdoe-right-sticky').removeClass('top_small_affixed');
			}
			t('.fdoe-right-sticky').off('aroaffixed.bs.aroaffix').on('aroaffixed.bs.aroaffix', function() {
				t(this).aroaffix('checkPosition');
			});

			if (window.matchMedia('(min-width: 768px)').matches) {
				var right_aroaffix_top_ = t('.fdoe-top-sticky').css('top') != null ? t('.fdoe-top-sticky').css('top').replace("px", "") : 0;
				t(".fdoe-top-sticky").css('top', get_sticky_top(0) + 'px');
				t(".fdoe-top-sticky").aroaffix({
					offset: {
						top: function() {
							return (this.top = t('.fdoe-top-sticky').offset().top)
						},
						bottom: function() {
							return (this.bottom = t('footer').outerHeight(true))
						}
					}
				});
				var parentwidth_ = t(".fdoe").width();
				t(".fdoe-top-sticky").width(parentwidth_);
			}
			t(".fdoe-sticky").css('top', get_sticky_top(right_aroaffix_top) + 'px');
			t(document).on('aroaffix.bs.aroaffix', '.fdoe-top-sticky', function() {
				t(this).css('top', get_sticky_top(0) + 'px');
			});
			t(document).on('aroaffix.bs.aroaffix', '.fdoe-sticky', function() {
				t(this).css('top', get_sticky_top(right_aroaffix_top) + 'px');
			});
			t(".fdoe-sticky").aroaffix({
				offset: {
					top: function() {
						return (this.top = t('.fdoe-sticky').offset().top)
					},
					bottom: function() {
						return (this.bottom = t('footer').outerHeight(true))
					}
				}
			});
			var parentwidth2 = t("#fdoe-left-left-container").width();
			t("#menu_headings_2").width(parentwidth2);
		}
		callback_activate_scroll();
	}

	function init_event_list_mobile() {
		t('.fdoe-right-sticky').off('aroaffix.bs.aroaffix').on('aroaffix.bs.aroaffix', function() {
			var bg_el_color = t('body').css('background-color');
			if (fdoe.sticky_mobile == 'cats') {
				t('#fdoe_checker_big_devices').hide();
				t('.fdoe_order_time').hide();
			}
			t(this).css('background-color', bg_el_color);
			if (fdoe.top_bar_mobile == 1 && (fdoe.sticky_mobile == 'both' || fdoe.sticky_mobile == 'selector')) {
				t('#fdoe_checker_top-bar').addClass('top-bar-sticky').prependTo('.fdoe-right-sticky');
				t('.top-bar-info').appendTo('#fdoe_checker_top-bar');
				if (fdoe.sticky_mobile == 'both') {
					t('#menu_headings').detach().hide().addClass('menu_headings_fixed').appendTo('.fdoe-right-sticky').css('background-color', bg_el_color).show();
				}
				t(this).addClass('top_small_affixed');
			} else if (fdoe.top_bar_mobile == 1 && (fdoe.sticky_mobile == 'both' || fdoe.sticky_mobile == 'cats')) {
				t('#menu_headings').detach().hide().addClass('menu_headings_fixed').appendTo('.fdoe-right-sticky').css('background-color', bg_el_color).show();
				t(this).addClass('top_small_affixed');
			}
		});
		t('.fdoe-right-sticky').off('aroaffix-top.bs.aroaffix').on('aroaffix-top.bs.aroaffix', function() {
			if (fdoe.sticky_mobile == 'cats') {
				t('#fdoe_checker_big_devices').show();
				t('.fdoe_order_time').show();
			}
			if (fdoe.top_bar_mobile == 1 && (fdoe.sticky_mobile == 'both' || fdoe.sticky_mobile == 'selector')) {
				t('#fdoe_checker_top-bar').removeClass('top-bar-sticky').prependTo('.fdoe-top-bar-header');
				t('.top-bar-info').insertBefore('.top-bar-cart');
				if (fdoe.sticky_mobile == 'both') {
					t('#menu_headings').detach().hide().removeClass('menu_headings_fixed').prependTo('#fdoe-left-container').css('display', 'flex');
				}
				t(this).removeClass('top_small_affixed');
			} else if (fdoe.top_bar_mobile == 1 && (fdoe.sticky_mobile == 'both' || fdoe.sticky_mobile == 'cats')) {
				t('#menu_headings').detach().hide().removeClass('menu_headings_fixed').prependTo('#fdoe-left-container').css('display', 'flex');
				t(this).removeClass('top_small_affixed');
			}
		});
	}

	function get_sticky_top(right_aroaffix_top) {
		var extra_header = t('.nav-container.fixed').css('position') == 'fixed' ? t('.nav-container.fixed').outerHeight(true) : 0;
		var iii = !window.matchMedia('(max-width: 600px)').matches ? t('#wpadminbar').height() : 0;
		var eee = t('header').css('position') == 'fixed' ? t('header').outerHeight(true) - iii : 0;
		right_aroaffix_top = (!window.matchMedia('(min-width: 768px)').matches && fdoe.sticky_mobile !== 'no') ? 0 : right_aroaffix_top;
		var fdoe_top = parseFloat(right_aroaffix_top) + eee + extra_header + iii;
		return fdoe_top;
	}

	function do_scrolling(right_aroaffix_top) {
		t('.fdoe-right-sticky').off('aroaffix.bs.aroaffix').on('aroaffix.bs.aroaffix', function() {
			gateup = true;
			gatedown = true;
		});
		t('.fdoe-right-sticky').one('aroaffix.bs.aroaffix', function() {
			t('.fdoe-right-sticky.aroaffix').css({
				'top': get_sticky_top(right_aroaffix_top) + 'px'
			});
		});
		var lastScrollTop = t('.fdoe-sticky').scrollTop();
		var user_scroll = true;
		var atop, iii2, eee2, extra_header2, up_active, css_obj;
		var win_height = t(window).height();
		gateup = true;
		gatedown = true;
		t(window).off('scroll.test').on('scroll.test', function() {
			if (user_scroll) {
				var st = t(this).scrollTop();
				if (st > lastScrollTop && gatedown) {
					// downscroll code
					iii2 = t('#wpadminbar').height();
					eee2 = t('header').css('position') == 'fixed' ? t('header').outerHeight(true) : 0;
					extra_header2 = t('.nav-container.fixed').css('position') == 'fixed' ? t('.nav-container.fixed').outerHeight(true) : 0;
					if ((win_height - eee2 - extra_header2 - iii2) < t('.fdoe-right-sticky').outerHeight(true)) {
						atop = win_height - t('.fdoe-right-sticky').outerHeight(true);
						t('.fdoe-right-sticky.aroaffix').css({
							'top': atop + 'px'
						});
						lastScrollTop = st;
						gateup = true;
						gatedown = false;
					} else {
						lastScrollTop = st;
						gateup = true;
						gatedown = true;
					}
				} else if (st < lastScrollTop && gateup) {
					// upscroll code
					up_active = t(window).scrollTop() + t('.fdoe-right-sticky').outerHeight(true) + 30 < t('#fdoe-left-container').offset().top + t('#fdoe-left-container').outerHeight(true);
					css_obj = up_active ? {
						'top': get_sticky_top(right_aroaffix_top) + 'px'
					} : {
						'top': win_height - t('.fdoe-right-sticky').outerHeight(true) + 'px'
					};
					t('.fdoe-right-sticky.aroaffix').css(
						css_obj
					);
					lastScrollTop = st;
					gatedown = true;
					gateup = up_active ? false : true;
				} else {
					user_scroll = true;
					lastScrollTop = st;
				}
			}
			t('.fdoe-sticky.aroaffix').css(
				get_sticky_top(right_aroaffix_top) + 'px'
			);
		});
	}
	// Adding scrollspy for Menu category
	function activate_scroll() {
		if (fdoe.is_accordian == 1) {
			return;
		}
		var freeze = false;
		var fdoe_timeout;
		t(window).on('scroll', function() {
			var currentTop = t(window).scrollTop();
			var elems = t('.scrollspy');
			elems.each(function() {
				var adjust_for_sticky = t('.top_small_affixed.aroaffix').outerHeight(true) + t('.fdoe-top-sticky.aroaffix').outerHeight(true);
				var elemTop = t(this).offset().top * 0.95 - adjust_for_sticky;
				var elemBottom = elemTop + t(this).outerHeight();
				var docHeight = t(document).height();
				var winScrolled = t(window).height() + t(window).scrollTop();
				if (freeze === false && ((t(this).is('.scrollspy:nth-last-child(2)') && (docHeight - winScrolled) < t(this).outerHeight()) || (t(this).is('.scrollspy:last-child') && (docHeight - winScrolled) < 1) || currentTop >= elemTop && currentTop <= elemBottom)) {
					var id = t(this).attr('id');
					var navElem = t('#menu_headings_2 a[href="#' + id + '"]');
					navElem.parent().addClass('fdoe-active-link').siblings().removeClass('fdoe-active-link');
					var navElem2 = t('#menu_headings a[href="#' + id + '"]');
					navElem2.addClass('fdoe-active-link-2').parent().siblings().find('a').removeClass('fdoe-active-link-2');
				}
			});
		});
		t('.fdoe_menuitem a').off('click').on('click', function() {
			clearTimeout(fdoe_timeout);
			freeze = true;
			t(this).parent('.fdoe_menuitem').addClass('fdoe-active-link').siblings().removeClass('fdoe-active-link');
			fdoe_timeout = setTimeout(function() {
				t('.fdoe_menuitem').trigger('fdoe_clicked_');
			}, 2000);
		});
		t('#menu_headings a').off('click').on('click', function() {
			clearTimeout(fdoe_timeout);
			freeze = true;
			t(this).addClass('fdoe-active-link-2').removeClass('fdoe-temp-class').parent().siblings().find('a').removeClass('fdoe-active-link-2').removeClass('fdoe-temp-class');
			fdoe_timeout = setTimeout(function() {
				t('#menu_headings a').trigger('fdoe_clicked_2');
			}, 2000);
		});
		t('.fdoe_menuitem').on('fdoe_clicked_', function() {
			freeze = false;
			clearTimeout(fdoe_timeout);
		});
		t('#menu_headings a').on('fdoe_clicked_2', function() {
			freeze = false;
			clearTimeout(fdoe_timeout);
		});
		t('#menu_headings a').hover(function() {
			t(this).parent().siblings().find('a.fdoe-active-link-2').toggleClass('fdoe-active-link-2').toggleClass('fdoe-temp-class');
		}, function() {
			t(this).parent().siblings().find('a.fdoe-temp-class').toggleClass('fdoe-active-link-2').toggleClass('fdoe-temp-class');
		});
	}

	function do_style() {
		if (((window.matchMedia('(max-width: 767px)').matches) && fdoe.top_bar_mobile == 1) || ((!window.matchMedia('(max-width: 767px)').matches && fdoe.top_bar_menu == 1))) {
			jQuery('#menu_headings').css('display', 'flex');
		}
		if (jQuery('ul#menu_headings li').length > 0 || jQuery('#menu_headings_2 div').length > 0) {
			jQuery('.fdoe-item-icon').css("color", fdoe.menu_color);
			jQuery('.fdoe-menu-title-icon').css("color", fdoe.menu_color);
			jQuery('#menu_headings  a').css("color", fdoe.menu_color);
			jQuery('#menu_headings_2  a').css("color", fdoe.menu_color);
			if (jQuery('ul#menu_headings li').length == 1) {
				jQuery("#menu_headings").hide();
			}
		}

		jQuery('input.qty').addClass('features-form');
		/* CSS Modifications */
		/* Detach the Woocommerce products Header */
		if (!jQuery.trim(jQuery(".woocommerce-products-header").html())) {
			jQuery('.woocommerce-products-header').detach();
		}
		// Layout options CSS
		if (fdoe.layout == 'fdoe_twocols') {
			jQuery(".fdoe-item  .flex-container-row").append("<div class='fdoe_aggregate_row'></div>");
			jQuery('.fdoe-item  .flex-container-row .fdoe_thumb').each(function() {
				jQuery(this).parent().find('.fdoe_aggregate_row').append(jQuery(this));
			});
			jQuery('.fdoe-item  .flex-container-row .fdoe_item_price').each(function() {
				jQuery(this).parent().find('.fdoe_aggregate_row').append(jQuery(this));
			});
			jQuery('.fdoe-item  .flex-container-row .fdoe_add_item').each(function() {
				jQuery(this).parent().find('.fdoe_aggregate_row').append(jQuery(this));
			});
			jQuery('.fdoe_aggregate_row').wrap("<div class='fdoe_second_row'></div>");
			jQuery('.fdoe-item  .flex-container-row .fdoe_aggregate_row').each(function() {
				jQuery(this).find('.fdoe_add_price_item').wrapAll("<span class='fdoe_price_and_add'></div>");
			});
			jQuery('.fdoe_summary').css("margin-right", 'unset');
			jQuery('.fdoe_title').css("text-align", 'center');
			jQuery('.flex-container-row').css("align-items", 'unset');
			jQuery('.flex-container-row').css("flex-direction", 'column');
			jQuery('.fdoe_summary').css("order", '0');
			jQuery(".fdoe_aggregate_row").css("order", '1');
			if (fdoe.fdoe_show_images == 'hide') {
				jQuery(".fdoe_aggregate_row").css("justify-content", 'space-around');
			}
			if (fdoe.fdoe_item_border == 'hide') {
				jQuery(".fdoe_aggregate_row").css("justify-content", 'space-around');
			}
		} else if (fdoe.layout == 'fdoe_twentytwenty') {
			if (!(window.matchMedia('(max-width: 767px)').matches) && t('.fdoe').width() > 600) {
				jQuery(window).on("load", function() {
					jQuery('#the_menu .cat_tbody.aro-style-twenty').not('.twenty-sub-cat').each(function(i) {
						var $this = this;
						var count = t($this).not('.twenty-sub-cat').children("div.fdoe-item").length;
						if (count === 0) {
							t($this).not('.twenty-sub-cat').find('.menu_titles_image_main').remove();
						}
						var heights = t($this).not('.twenty-sub-cat').find("div.fdoe-item").not(":first").map(function() {
							return t(this).height();
						}).get();
						var h = Math.max.apply(null, heights);
						t($this).not('.twenty-sub-cat').find('.menu_titles_image'). /*appendTo(t($this)).*/ css('grid-row-end', function() {
							var yy = t($this).not('.twenty-sub-cat').find('.menu_titles_image img').height();
							var span = Math.round((yy / h));
							return 'span ' + span;
						}).promise().done(function() {
							t($this).css('visibility', 'visible');
						});
					});
					if (fdoe.is_accordian == 0) {
						jQuery('.cat_tbody.aro-style-twenty.twenty-sub-cat').each(function(i) {
							var $this2 = this;
							var heights = t($this2).find("div.fdoe-item").not(":first").map(function() {
								return t(this).height();
							}).get();
							var h = Math.max.apply(null, heights);
							t($this2).find('.menu_titles_image'). /*appendTo(t($this)).*/ css('grid-row-end', function() {
								var yy = t($this2).find('.menu_titles_image img').height();
								var span = Math.round((yy / h));
								return 'span ' + span;
							}).promise().done(function() {
								t($this2).css('visibility', 'visible');
							});
						});
					}
				});
				jQuery('.aro-style-twenty.arocollapse').on('show.bs.arocollapse', function() {
					t(this).find("div.fdoe-item").css('visibility', 'hidden');
				});
				jQuery('.aro-style-twenty.arocollapse').on('shown.bs.arocollapse', function() {
					var $this = this;
					var heights = t($this).not('.twenty-sub-cat').children("div.fdoe-item").not(":first").map(function() {
						return t(this).height();
					}).get();
					var h = Math.min.apply(null, heights);
					t($this).not('.twenty-sub-cat').children('.menu_titles_image').css('grid-row-end', function() {
						var yy = t(this).children('.menu_titles_image img').height();
						var span = Math.round((yy / h));
						return 'span ' + span;
					}).promise().done(function() {
						t($this).css('visibility', 'visible');
						t($this).not('.twenty-sub-cat').find("div.fdoe-item").css('visibility', 'visible');
					});
					jQuery($this).find('.cat_tbody.aro-style-twenty.twenty-sub-cat').each(function(i) {
						var $this2 = this;
						var heights = t($this2).find("div.fdoe-item").not(":first").map(function() {
							return t(this).height();
						}).get();
						var h = Math.max.apply(null, heights);
						t($this2).find('.menu_titles_image').css('grid-row-end', function() {
							var yy = t($this2).find('.menu_titles_image img').height();
							var span = Math.round((yy / h));
							return 'span ' + span;
						}).promise().done(function() {
							t($this2).css('visibility', 'visible');
						});
					});
				});
			} else {
				jQuery('.cat_tbody').addClass('twentytwenty_small_screen');
				t('.aro-style-twenty').css('visibility', 'visible');
			}
			// Category Menu
			if (((window.matchMedia('(max-width: 767px)').matches) && fdoe.top_bar_mobile == 1) || ((!window.matchMedia('(max-width: 767px)').matches && fdoe.top_bar_menu == 1))) {
				jQuery('.fdoe_menu_header').fadeIn();
			} else {}
		}
		//
		// Hide Minicart option
		if (fdoe.hide_minicart == 'yes' && (typeof fdoedel === "undefined" || (typeof fdoedel !== "undefined" && fdoedel.fdoe_enable_delivery_switcher == 'no'))) {
			if (fdoe.show_left_menu == 1) {
				jQuery("#fdoe-left-container").toggleClass('arocol-xs-12 arocol-sm-9 arocol-lg-9', false);
				jQuery("#fdoe-left-container").toggleClass('arocol-xs-12 arocol-sm-12 arocol-lg-12', true);
				jQuery(".fdoe-flex-1").addClass('fdoe-only-menu');
				jQuery('.fdoe_extra_checkout').css('display', 'block').addClass('fdoe-extra-checkout-flex');
				jQuery('.fdoe_order_time').prependTo('.fdoe-flex-1').removeClass('fdoe_hidden').fadeIn('slow');
			} else {
				jQuery("#fdoe-left-container").toggleClass('arocol-sm-7 arocol-lg-7', false);
				jQuery("#fdoe-left-container").toggleClass('arocol-sm-9 arocol-lg-9', true);
				jQuery("#fdoe-left-left-container").toggleClass('arocol-sm-2', false);
				jQuery("#fdoe-left-left-container").toggleClass('arocol-sm-3', true);
				if (fdoe.top_bar == 0) {
					jQuery('.fdoe_extra_checkout').css('margin-left', '0px').css('float', 'none').prependTo('#fdoe-left-left-container').show();
				}
				jQuery('.fdoe_order_time').prependTo('#fdoe-left-left-container').removeClass('fdoe_hidden').fadeIn('slow');
			}
			if (fdoe.top_bar == 0) {} else {
				jQuery('.fdoe-top-bar-header').css({
					'grid-template-columns': 'auto'
				});
				jQuery('.fdoe_pickup_time').css({
					'margin-right': '1em'
				});
				jQuery('.fdoe_order_time').css({
					'display': 'flex',
					'align-items': 'center',
					'min-width': '7em'
				});
				jQuery(".fdoe_order_time").prependTo('.top-bar-place-right').css({}).addClass('top-bar-icon').removeClass('fdoe_hidden').fadeIn('slow');
			}
			jQuery("#fdoe-right-container").remove();
		} else if (fdoe.hide_minicart == 'yes' && (typeof fdoedel === "undefined" || (typeof fdoedel !== "undefined" && fdoedel.fdoe_enable_delivery_switcher == 'yes'))) {
			jQuery(".fdoe_mini_cart_outer").remove();
			if (fdoe.top_bar == 0) {
				jQuery(".fdoe_order_time").removeClass('fdoe_hidden').fadeIn('slow');
				jQuery('.fdoe_extra_checkout').addClass('fdoe_sole_chk_btn').appendTo('#fdoe-right-container').show();
			} else {
				if (fdoe.show_left_menu == 1) {
					jQuery("#fdoe-left-container").toggleClass('arocol-xs-12 arocol-sm-9 arocol-lg-9', false);
					jQuery("#fdoe-left-container").toggleClass('arocol-xs-12 arocol-sm-12 arocol-lg-12', true);
				} else {
					jQuery("#fdoe-left-container").toggleClass('arocol-sm-7 arocol-lg-7', false);
					jQuery("#fdoe-left-container").toggleClass('arocol-sm-9 arocol-lg-9', true);
					jQuery("#fdoe-left-left-container").toggleClass('arocol-sm-2', false);
					jQuery("#fdoe-left-left-container").toggleClass('arocol-sm-3', true);
				}
				jQuery('.fdoe_order_time').css({
					'display': 'flex',
					'align-items': 'center',
					'min-width': '7em'
				});
				jQuery(".fdoe_order_time").prependTo('.top-bar-place-right').css({}).addClass('top-bar-icon').removeClass('fdoe_hidden').fadeIn('slow');
			}
			jQuery("#fdoe-right-container").fadeIn('slow');
		} else {
			jQuery("#fdoe-right-container").fadeIn('slow');
			if (fdoe.top_bar == 0) {
				jQuery(".fdoe_order_time").removeClass('fdoe_hidden').fadeIn('slow');
			} else {
				jQuery('.fdoe_order_time').css({
					'display': 'flex',
					'align-items': 'center',
					'min-width': '7em',
					'justify-content': 'center'
				});
				jQuery(".fdoe_order_time").prependTo('.top-bar-place-right').css({}).addClass('top-bar-icon').removeClass('fdoe_hidden').fadeIn('slow');
			}
		}
		if (fdoe.top_bar == 1) {
			jQuery('.fdoe-top-bar-header').css({
				'display': 'grid'
			});
			var size = t('.fdoe-top-bar-header').children().length;
			if (size < 2) {
				jQuery('.fdoe-top-bar-header').css({
					'grid-template-columns': 'auto'
				});
				jQuery('.top-bar-place-right').css({
					'border-bottom': 'unset',
					'margin-bottom': '0',
					'padding-bottom': '5px',
					'padding-top': '5px'
				});
			}
			if ((window.matchMedia('(max-width: 767px)').matches)) {
				jQuery('.fdoe-top-bar-header').addClass('fdoe-top-bar-header-small');
				jQuery('.fdoe-flex-1').addClass('top-bar-fdoe-flex-1');
			} else {
				jQuery('.fdoe-top-bar-header').removeClass('fdoe-top-bar-header-small');
				jQuery('.fdoe-flex-1').removeClass('top-bar-fdoe-flex-1');
			}
		}
		// Theme fixes for known problem with certain Themes
		if (fdoe.theme == 'Bridge' || fdoe.theme_parent == 'Bridge') {
			jQuery('.fdoe-aromodal').on('shown.bs.aromodal', function() {
				jQuery('body').addClass('fdoe-aromodal-open');
			});
			jQuery('.fdoe-aromodal').on('hidden.bs.aromodal', function() {
				jQuery('body').removeClass('fdoe-aromodal-open');
			});
		}
	}
});
