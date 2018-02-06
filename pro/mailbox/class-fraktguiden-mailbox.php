<?php
// @todo: mailinglist: sassa@pagelook.no <- wants this ASAP
//
/**
 * Fraktguiden Mailbox
 * https://developer.bring.com/api/order/to-mailbox/
 */
class Fraktguiden_Mailbox {


// Example json:
/**

// Orders ( label + booking )
https://api.bring.com/order/to-mailbox/
Request:
{
  "data": {
    "type": "orders",
    "attributes": {
      "customerNumber": "PARCELS_NORWAY-1234567890",
      "senderName": "Posten Norge AS",
      "postalCode": "0185",
      "streetAddress": "Biskop Gunnerus' gate 14A",
      "senderEmail": "sender@example.com",
      "reference": "Fredag uke 35",
      "testIndicator": true,
      "packages": [
        {
          "rfid": true,
          "weight": 1200,
          "recipientName": "Arne Andersen",
          "postalCode": "9409",
          "streetAddress": "Brurvikvegen 10c",
          "phoneNumber": "+47 400 00 123",
          "email": "recipient@example.com"
        },
        {
          "rfid": true,
          "weight": 350,
          "recipientName": "Berit Burger",
          "postalCode": "5018",
          "streetAddress": "Heggebakken 1",
          "phoneNumber": "+47 900 00 123",
          "email": "recipient@example.com"
        },
        {
          "rfid": true,
          "weight": 800,
          "recipientName": "Charles Caspersen",
          "postalCode": "0650",
          "streetAddress": "Åkebergveien 56A",
          "phoneNumber": "+47 400 01 234",
          "email": "recipient@example.com"
        },
        {
          "rfid": false,
          "weight": 200,
          "recipientName": "Dina Davidsen",
          "postalCode": "1337",
          "streetAddress": "Øvre torv 2",
          "phoneNumber": "+47 900 01 234",
          "email": "recipient@example.com"
        },
        {
          "rfid": false,
          "weight": 150,
          "recipientName": "Eskil Erlandsen",
          "postalCode": "0666",
          "streetAddress": "Klosterheimveien 14",
          "phoneNumber": "+47 400 02 345",
          "email": "recipient@example.com"
        }
      ]
    }
  }
}

Response 201:
{
  "data": {
    "type": "order",
    "attributes": {
      "customerName": "Posten Norge AS",
      "customerOrganizationNumber": "984661185",
      "customerNumber": "PARCELS_NORWAY-1234567890",
      "senderName": "Posten Norge AS",
      "streetAddress": "Biskop Gunnerus' gate 14A",
      "postalCode": "0185",
      "postalPlace": "OSLO",
      "email": "sender@example.com",
      "reference": "Fredag uke 35",
      "priceWithoutVat": 189.45,
      "priceWithVat": 236.81,
      "vat": 47.36,
      "currency": "NOK",
      "orderTime": "2017-09-01T15:29:32.000+02:00",
      "testIndicator": true,
      "packages": [
        {
          "rfid": true,
          "recipientName": "Arne Andersen",
          "streetAddress": "Brurvikvegen 10c",
          "postalCode": "9409",
          "postalPlace": "HARSTAD",
          "phoneNumber": "+47 400 00 123",
          "email": "recipient@example.com",
          "weight": 1200,
          "agreementNumber": false
        },
        {
          "rfid": true,
          "recipientName": "Berit Burger",
          "streetAddress": "Heggebakken 1",
          "postalCode": "5018",
          "postalPlace": "BERGEN",
          "phoneNumber": "+47 900 00 123",
          "email": "recipient@example.com",
          "weight": 350,
          "agreementNumber": false
        },
        {
          "rfid": true,
          "recipientName": "Charles Caspersen",
          "streetAddress": "Åkebergveien 56A",
          "postalCode": "0650",
          "postalPlace": "OSLO",
          "phoneNumber": "+47 400 01 234",
          "email": "recipient@example.com",
          "weight": 800,
          "agreementNumber": false
        },
        {
          "rfid": false,
          "recipientName": "Dina Davidsen",
          "streetAddress": "Øvre torv 2",
          "postalCode": "1337",
          "postalPlace": "SANDVIKA",
          "phoneNumber": "+47 900 01 234",
          "email": "recipient@example.com",
          "weight": 200,
          "agreementNumber": false
        },
        {
          "rfid": false,
          "recipientName": "Eskil Erlandsen",
          "streetAddress": "Klosterheimveien 14",
          "postalCode": "0666",
          "postalPlace": "OSLO",
          "phoneNumber": "+47 400 02 345",
          "email": "recipient@example.com",
          "weight": 200,
          "agreementNumber": false
        }
      ]
    }
  }
}

Error 422:
{
  "errors": [
    {
      "code": "8000",
      "title": "Invalid phone number",
      "source": {
        "pointer": "/packages/0/phoneNumber"
      }
    }
  ]
}

# Labels only
https://api.bring.com/order/to-mailbox/labels
Request:
{
  "data": {
    "type": "labels",
    "attributes": {
      "customerNumber": "PARCELS_NORWAY-XXXXXXXXXXXXX",
      "packages": [
        {
          "recipientName": "Pooja",
          "streetAddress": "AA",
          "postalCode": "0150",
          "email": "a@b.com",
          "phoneNumber": "+4799999999",
          "weight": 200
        },
        {
          "recipientName": "Pooja",
          "streetAddress": "BB",
          "postalCode": "0150",
          "email": "a@b.com",
          "phoneNumber": "+4799999999",
          "weight": 200
        }
      ],
      "senderName": "Self",
      "senderEmail": "abc@gmail.com",
      "streetAddress": "A-905",
      "postalCode": "0150",
      "reference": "123456"
    }
  }
}

Response 201:
{
  "data": {
    "attributes": {
      "reference": "123456",
      "senderName": "Self",
      "streetAddress": "A-905",
      "email": "abc@gmail.com",
      "packages": [
        {
          "priority": "",
          "rfid": false,
          "recipientName": "Pooja",
          "streetAddress": "AA",
          "postalCode": "0150",
          "postalPlace": "OSLO",
          "phoneNumber": "+4799999999",
          "email": "a@b.com",
          "weight": 200,
          "shipmentNumber": "70438101412777671",
          "packageNumber": "LA297895234NO",
          "priceType": "AGREEMENT_PRICE"
        },
        {
          "priority": "",
          "rfid": false,
          "recipientName": "Pooja",
          "streetAddress": "BB",
          "postalCode": "0150",
          "postalPlace": "OSLO",
          "phoneNumber": "+4799999999",
          "email": "a@b.com",
          "weight": 200,
          "shipmentNumber": "70438101412777688",
          "packageNumber": "LA297895248NO",
          "priceType": "AGREEMENT_PRICE"
        }
      ],
      "labelUri": "https://api.bring.com/labels/id/42ebd18a-e91b-497b-bca4-bede4ee41731.pdf",
      "rfidLabelUri": "",
      "orderTime": "2017-09-12T12:50:13.395+02:00",
      "postalCode": "0150",
      "postalPlace": "OSLO",
      "customerName": "XXXXXXXXXXXXX",
      "customerOrganizationNumber": "XXXXXXXXXXXXX",
      "customerNumber": "PARCELS_NORWAY-XXXXXXXXXXX"
    },
    "type": "labels"
  }
}
Error 422:
{
  "errors": [
    {
      "code": "8000",
      "title": "Invalid phone number",
      "source": {
        "pointer": "/packages/0/phoneNumber"
      }
    }
  ]
}
Error 500:
{
  "errors":[
    {
      "code": "500",
      "title": "An unknown error occured",
      "source": {
        "pointer": "/"
      }
    }
  ]
}

# Book a previosuly labelled order
https://api.bring.com/order/to-mailbox/label/order
Request:
{
  "data": {
    "type": "label_orders",
    "attributes": {
      "customerNumber": "PARCELS_NORWAY-XXXXXXXXXXXXX",
      "packageNumbers": [
        "LA297895248NO"
      ]
    }
  }
}
Response 201:
{
  "data": {
    "attributes": {
      "id": 3115,
      "customerName": "GET INSPIRED AS",
      "customerOrganizationNumber": "912636712",
      "customerNumber": "PARCELS_NORWAY-10019415859",
      "senderName": "Self",
      "streetAddress": "A-905",
      "postalCode": "0150",
      "postalPlace": "OSLO",
      "email": "abc@gmail.com",
      "reference": "123456",
      "packages": [
        {
          "priority": "",
          "rfid": false,
          "recipientName": "Pooja",
          "streetAddress": "AA",
          "postalCode": "0150",
          "postalPlace": "OSLO",
          "phoneNumber": "+4799999999",
          "email": "a@b.com",
          "weight": 200,
          "shipmentNumber": "70438101412779446",
          "packageNumber": "LA297895248NO",
          "priceType": "AGREEMENT_PRICE"
        }
      ],
      "labelUri": "https://api.bring.com/labels/id/f57ec601-5c33-4128-abe7-da80c70f99d1.pdf",
      "rfidLabelUri": "",
      "waybillUri": "https://api.bring.com/labels/id/187a6a51-26b5-4345-82a9-6429001e7c3b.pdf",
      "priceWithoutVat": 150,
      "priceWithVat": 187.5,
      "vat": 37.5,
      "currency": "NOK",
      "orderTime": "2017-09-13T13:25:11.875363Z",
      "orderNumberReference": "20002880"
    },
    "type": "order",
    "id": "3115"
  }
}
Error 422:
{
  "errors": [
    {
      "code": "8000",
      "title": "Invalid phone number",
      "source": {
        "pointer": "/packages/0/phoneNumber"
      }
    }
  ]
}

# Fetch an order
https://api.bring.com/order/to-mailbox/{orderId}
Response:
{
  "data": {
    "type": "order",
    "id": "2664",
    "attributes": {
      "id": 2664,
      "customerName": "Posten Norge AS",
      "customerOrganizationNumber": "984661185",
      "customerNumber": "PARCELS_NORWAY-1234567890",
      "senderName": "Posten Norge AS",
      "streetAddress": "Biskop Gunnerus' gate 14A",
      "postalCode": "0185",
      "postalPlace": "OSLO",
      "email": "sender@example.com",
      "reference": "Fredag uke 35",
      "priceWithoutVat": 189.45,
      "priceWithVat": 236.81,
      "vat": 47.36,
      "currency": "NOK",
      "orderTime": "2017-09-01T11:59:32.410Z",
      "labelUri": "",
      "rfidLabelUri": "https://example.com/17673176-8531-48dd-b407-d84bb2f26a8b.pdf",
      "waybillUri": "https://example.com/bafc82c5-e8c2-4abf-4c0a-c40ff06eb4fa.pdf",
      "orderNumberReference": "20002429",
      "testIndicator": true,
      "packages": [
        {
          "rfid": false,
          "recipientName": "Arne Andersen",
          "streetAddress": "Brurvikvegen 10c",
          "postalCode": "9409",
          "postalPlace": "HARSTAD",
          "phoneNumber": "+47 400 00 123",
          "email": "recipient@example.com",
          "weight": 1200,
          "shipmentNumber": "70438101412766736",
          "packageNumber": "LA297886201NO",
          "agreementNumber": false
        },
        {
          "rfid": false,
          "recipientName": "Berit Burger",
          "streetAddress": "Heggebakken 1",
          "postalCode": "5018",
          "postalPlace": "BERGEN",
          "phoneNumber": "+47 900 00 123",
          "email": "recipient@example.com",
          "weight": 350,
          "shipmentNumber": "70438101412766743",
          "packageNumber": "LA297886215NO",
          "agreementNumber": false
        },
        {
          "rfid": false,
          "recipientName": "Charles Caspersen",
          "streetAddress": "Åkebergveien 56A",
          "postalCode": "0650",
          "postalPlace": "OSLO",
          "phoneNumber": "+47 400 01 234",
          "email": "recipient@example.com",
          "weight": 800,
          "shipmentNumber": "70438101412766750",
          "packageNumber": "LA297886229NO",
          "agreementNumber": false
        },
        {
          "rfid": false,
          "recipientName": "Dina Davidsen",
          "streetAddress": "Øvre torv 2",
          "postalCode": "1337",
          "postalPlace": "SANDVIKA",
          "phoneNumber": "+47 900 01 234",
          "email": "recipient@example.com",
          "weight": 200,
          "shipmentNumber": "70438101412766767",
          "packageNumber": "LA297886232NO",
          "agreementNumber": false
        },
        {
          "rfid": false,
          "recipientName": "Eskil Erlandsen",
          "streetAddress": "Klosterheimveien 14",
          "postalCode": "0666",
          "postalPlace": "OSLO",
          "phoneNumber": "+47 400 02 345",
          "email": "recipient@example.com",
          "weight": 200,
          "shipmentNumber": "70438101412766774",
          "packageNumber": "LA297886246NO",
          "agreementNumber": false
        }
      ]
    }
  }
}
 */
}