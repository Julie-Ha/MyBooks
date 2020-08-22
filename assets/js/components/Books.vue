<template>
	<div>
		<div v-for="book in books" :key="book.id">
			<Book :book="book" />
		</div>
		<h3 class="text-center" v-if="loading">Chargement des livres...</h3>
	</div>
</template>

<script>
	import axios from 'axios'
	import Book from './Book'

	export default {
		components: {Book},
		data() {
			return {
				books: [],
				total: 0,
				offset: 0,
				loading: true
			}
		},
		async mounted() {
			let { data } = await axios.get('/api/getBooks')
			
			this.books = data.books
			this.total = data.total
			this.offset = data.books.length

			this.loading = false

			window.addEventListener('scroll', async (e) => {
				//Obligé de mettre +1 car il y a un décalage de 1 pixel ? Voir plus tard d'où ça vient... 
				if ((window.innerHeight + window.pageYOffset + 1 >= document.body.offsetHeight) && this.offset < this.total) {
					this.loading = true
					let { data } = await axios.get('/api/getBooks/' + this.offset)

					this.books = [...this.books, ...data.books]
					this.offset += data.books.length

					this.loading = false
				}
			})
		}
	}
</script>