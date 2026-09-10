# The customer number in a booking

The Booking API takes the customer number as the plain number, in
`consignments[].product.customerNumber`.

The number `2` is the shared test customer of Bring, named
"Demo Parcel Norway Customer (2)". Use it to try a booking without a real
account. A shop must never keep it in production.

A prefixed or padded form fails. `PARCELS_NORWAY-00000002` and `00000002` both
return `BOOK-INPUT-021 Invalid customer number`.

An account may hold several customer numbers. `customers.json` lists one entry
per country per customer, with the products of that customer. A number that is
not the main customer for the product returns
`BOOK_VALIDATION-017 Main customer number must be used for this service`, even
when the product stands in its list.

The mailbox products 3570 and 3584 are the exception. For those the plugin
strips a leading prefix from the number. See `Fraktguiden_Service::getProduct()`.

## Sources

- [Bring Developer, Booking API](https://developer.bring.com/api/booking/)
- `https://api.bring.com/booking/api/customers.json`
