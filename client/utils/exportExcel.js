import axios from 'axios'
import { saveAs } from 'file-saver'

export async function exportToExcel(params = {}) {
  try {
    const response = await axios.get('https://accas.ru/api/orders/export-inhouse', {
      responseType: 'blob',
      params,
    })

    const contentDisposition = response.headers['content-disposition']
    let filename = 'Orders.xlsx'
    if (contentDisposition) {
      const match = contentDisposition.match(/filename="?(.+)"?/)
      if (match && match[1]) {
        filename = decodeURIComponent(match[1])
      }
    }

    const blob = new Blob([response.data], {
      type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    })

    saveAs(blob, filename)
  } catch (err) {
    console.error('Error downloading Excel:', err)
  }
}
