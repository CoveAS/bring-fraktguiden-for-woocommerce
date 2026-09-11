# Step 3, API conversion: three user stories

These stories describe the wanted experience. They are inference, not a record
of the current build.

## The person

Kari owns a small shop. She sells goods from Norway. She installed WordPress,
WooCommerce and Bring Fraktguiden last week. She is not a developer.

She finished step 1 and step 2. The Get Started page now opens step 3.

## Before the stories: two names to fix

**"API conversion" means nothing to Kari.** She does not know what an API is.
She cannot tell whether this step is about money, about tax or about a
password. Name the step after the result she wants, for example "Connect your
Bring account".

**Bring asks for an email and an API key, not a password.** The Bring API signs
a request with the login email and with the API key. The field must say
"email", and the help text must say "the email you use to log in to Bring".
A field named "password" would send Kari to look for the wrong thing.

## Story A: Kari has the key and connects on the first try

Kari opens step 3. The card tells her, in one line, that Bring needs two
things: the email she logs in with, and an API key.

The card holds a button, "Open my Bring API settings". The button opens
https://www.mybring.com/useradmin/account/settings/api in a new tab.

Kari logs in to Bring. The page shows her API key. She copies it.

She goes back to the WordPress tab. The step is still open and her place is
kept. She pastes the key into the "API key" field.

She types her login email into the "Email" field. The help text under the
field says it must be the email she just logged in to Bring with.

She presses "Save and test". A short wait follows. The plugin asks Bring for a
price to prove the credentials work.

A green line appears: "Bring accepted your credentials." Step 3 closes with a
completed badge. Step 4 opens below it.

## Story B: Kari does not know which email to use

Kari has two addresses. One is her shop address, post@karisbutikk.no. One is
her personal address. She does not remember which one Bring knows.

Under the email field she sees a link: "Not sure? Find your login email on your
Bring profile." The link opens
https://www.mybring.com/useradmin/account/profile in a new tab.

If she is logged out, Bring asks her to log in. The login itself shows her the
right address.

The profile page shows the email. She copies it, returns to WordPress, pastes
it and presses "Save and test".

The test passes. The step completes.

## Story C: Bring rejects the credentials

Kari copied only part of the key. She presses "Save and test".

A red line appears: "Bring did not accept these credentials."

The plugin cannot tell which of the two fields is wrong. Bring answers with one
refusal for both. So the message gives Kari two things to check:

1. The API key is copied whole, with no space at either end.
2. The email is the one she logs in to Bring with, not her shop address.

The two links stay on the card, so she can open either page again.

The fields keep what she typed. The step stays open and stays incomplete.
Nothing is saved as working.

## What happens after the credentials work

The plugin can now ask Bring for live prices.

The completed badge on step 3 tells Kari the connection holds. The page moves
her to the next step, where she picks which Bring services her customers may
choose.

If the credentials stop working later, the step must be able to go back to
incomplete. A key can be removed on the Bring side.

## Open risks

1. **Two tabs.** Kari works in two tabs. Her WordPress session must survive the
   trip. Do not lose typed values on the way back.
2. **The copied key.** A key copied by hand often carries a space or a line
   break. Trim the value before the test.
3. **The wrong email.** This is the most common failure. The label and the help
   text carry the whole weight here.
4. **Bring is slow or down.** A failed test is not always a wrong key. Tell
   Kari when the problem is on the Bring side, and let her retry.
