let baseurl = $("#baseurl").data('baseurl');
//script flash message
function flasher(obj = $('#flasher-wrapper')) {
	// console.log('Flasher jalan');
	// console.log('Obj');
	let title = $('.flash-data', obj).data('title');
	let tipe = $('.flash-data', obj).data('icon');
	let pesan = $('.flash-data', obj).data('pesan');


	let title2 = $('.flash-data2', obj).data('title');
	let tipe2 = $('.flash-data2', obj).data('icon');
	let pesan2 = $('.flash-data2', obj).data('pesan');

	if (tipe && tipe2) {
		Swal.queue([{
			title: title,
			html: pesan,
			icon: tipe
		}, {
			title: title2,
			html: pesan2,
			icon: tipe2
		}]);
	}
	if (tipe && !tipe2) {
		Swal.queue([{
			title: title,
			html: pesan,
			icon: tipe
		}]);
	}
	if (!tipe && tipe2) {
		Swal.queue([{
			title: title2,
			html: pesan2,
			icon: tipe2
		}]);
	}

	$('#flasher-wrapper').html('');
}

flasher();

$(function () {
	// event hapus secara umum
	$('.container-fluid').parent().on('click', '.tombol-hapus', function () {
		event.preventDefault();
		let target = $(this).attr('href');
		// console.log('id_foto = ' + id_foto);
		Swal.fire({
			title: 'Hapus data ini?',
			text: "Data yang sudah dihapus tidak bisa dikembalikan.",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Hapus!',
			reverseButtons: true,
		}).then((result) => {
			// console.log(result.value);
			if (result.value) {
				location.href = target;
			}
		})
	});


	// event fitur in-development
	$("body").on("click", ".on-development", function () {
		event.preventDefault();
		Swal.fire({
			title: 'Peringatan!',
			text: "Fitur ini masih dalam masa pengembangan.",
			icon: 'warning',
			// showCancelButton: true,
			// confirmButtonColor: '#3085d6',
			// cancelButtonColor: '#d33',
			// confirmButtonText: 'Hapus!',
			// reverseButtons:true
		});
	});
});
