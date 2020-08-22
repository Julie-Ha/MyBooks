<template>
	<div>
		<article class="mr-auto ml-auto">
			<a class="book-a text-decoration-none text-dark" href="#">
				<img v-bind:src="'uploads/book_covers/' + book.imageName"/>
			</a>
		</article>
	</div>
</template>

<script>
	export default {
		props: {
			book: {
				required: true,
				type: Object
			}
		},
		mounted () {
			console.log(this.book)
		}
	}
</script>