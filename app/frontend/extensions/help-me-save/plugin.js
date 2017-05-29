(function ($, _, Backbone) {

	var FrameworkSettingsForm = Backbone.View.extend({

		el: 'form[name=woo-form]', 
		initialize: function(){
			this.attachFormChangeListener();
		},
		/**
		 * FormChangeListener()
		 *
		 * Listen for changes and run enableBeforeUnload()
		 *
		 * 
		 * @since 6.0.3
		 */
		 attachFormChangeListener: function(){
		 	
			 this.$el.on('change' , _.bind( function(e){
			 	 //enable the prompt
			 	 this.enableBeforeUnload();
			 }, this ));

		 },

		/**
		 * enableBeforeUnload()
		 *
		 * Enabling the "are you sure prompt" 
		 *
		 * user changes on a form in the settings sections will prompt 
		 * the user if they close the tab/window without saving their changes.
		 * 
		 * @since 6.0.3
		 */

		enableBeforeUnload: function() {
		    window.onbeforeunload = function (e) {
		        return "Discard changes?";
		    };
		},

		/**
		 * disableBeforeUnload()
		 *
		 * disable the "are you sure prompt" 
		 *
		 * The user has saved their changes and the prompt is no longer needed. 
		 * @since 6.0.3
		 */

		disableBeforeUnload: function() {
		    window.onbeforeunload = null;
		},

	});

	// initialize the script		
  window.frameworkSettingsform = new FrameworkSettingsForm();

}(jQuery, _ , Backbone  ) ); 