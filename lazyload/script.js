var lazyLoadInstance = new LazyLoad({
	elements_selector: ".lazy",
	load_delay: 300
});

if (lazyLoadInstance) {
	lazyLoadInstance.update();
}