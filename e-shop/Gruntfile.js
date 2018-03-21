/**
 * Author: Dominik Harmim <harmim6@gmail.com>
 */

module.exports = function (grunt) {
	require("load-grunt-tasks")(grunt);


	grunt.initConfig({
		nodeDir: "node_modules",
		jsDir: "www/js",
		cssDir: "www/css",
		fontsDir: "www/fonts",

		files: {
			js: [
				"<%= nodeDir %>/jquery/dist/jquery.slim.min.js",
				"<%= nodeDir %>/jquery-validation/dist/jquery.validate.min.js",
				"<%= nodeDir %>/popper.js/dist/umd/popper.min.js",
				"<%= nodeDir %>/bootstrap/dist/js/bootstrap.min.js"
			],
			css: [
				"<%= nodeDir %>/font-awesome/css/font-awesome.min.css",
				"<%= nodeDir %>/bootstrap/dist/css/bootstrap.min.css"
			],
			fonts: [
				"<%= nodeDir %>/font-awesome/fonts/*"
			]
		},

		clean: {
			js: [
				"<%= jsDir %>/*.js"
			],
			css: [
				"<%= cssDir %>/*.css"
			],
			fonts: [
				"<%= fontsDir %>"
			]
		},

		copy: {
			js: {
				files: [
					{src: "<%= files.js %>", dest: "<%= jsDir %>", expand: true, flatten: true}
				]
			},
			css: {
				files: [
					{src: "<%= files.css %>", dest: "<%= cssDir %>", expand: true, flatten: true}
				]
			},
			fonts: {
				files: [
					{src: "<%= files.fonts %>", dest: "<%= fontsDir %>", expand: true, flatten: true}
				]
			}
		}
	});


	grunt.registerTask("js", ["clean:js", "copy:js"]);
	grunt.registerTask("css", ["clean:css", "copy:css"]);
	grunt.registerTask("fonts", ["clean:fonts", "copy:fonts"]);
	grunt.registerTask("default", ["js", "css", "fonts"]);
};
