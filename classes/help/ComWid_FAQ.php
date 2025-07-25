<?php

class ComWid_FAQ implements ComWid
{
	private bool $displayed;
	private array $displayedQuestions;
	private array $questions;
	
	public function __construct()
	{
		$this->displayed = false;
		$this->displayedQuestions = [];
		
		$this->questions = [
			// 0
			<<<'EOQ'
				<h3>What is «Postal/Postcard Exchange»?</h3>
				<p>
					Postal exchange is the act of exchanging anything via post with strangers all over the globe. And
					postcard exchange is the subtype of the above, where postcards specifically are exchanged.
					Postcard exchange is often called «postcrossing» after the first postcard exchange website.
				</p>
			EOQ
			,
			// 1
			<<<'EOQ'
			<h3>How to get started?</h3>
			<p>
				First you need to <a href='/register'>register</a> and <a href='/login'>login</a>. After that you will
				be able to <a href='/send'>send postcards</a>
				(do not forget to write a code on in).
				At the same time other users will be sending cards to you,
				once you receive them, you <a href='/receive'>register those cards</a>.
			</p>
			EOQ
			,
			// 2
			<<<'EOQ'
			<h3>I am in a country with several spoken languages/used scripts. How should I write my address?</h3>
			<p>
				You should always have an address using Latin characters, regardless of your location. Whether we like it or not,
				this is a de facto standard. This site is written in English for a reason.
			</p>
			<p>
				If your country has other writing systems or spoken languages, please fill out the address in all of them.
				For example, if you are from Japan, write the address in rōmaji and then add another one in kanji, you can
				also add hiragana, katakana, and even hiragana.
				If you are from Belarus, write in Latin alphabet, and
				then both Russian and Belarussian in Cyrillic.
			</p>
			EOQ
			,
			// 3
			<<<'EOQ'
			<h3>Where to get postcards?</h3>
			<p>
				If you are just starting you can visit some book stores, that many times have some tourist postcards for
				your location. Museums also many times have cards for sale showing their expositions or depicting subjects
				related to theirs. Then you can check out online shops, there are many of them.
			</p>
			EOQ
			,
			// 4
			<<<'EOQ'
			<h3>Where to get postage?</h3>
			<p>
				This really does depend on where you are. Some countries will serve all your postage needs at a local
				postoffice, while others may require you to order stamps online (at least if you want to get interesting
				non-standard stamps).
			</p>
			<p>
				It is acceptable to use a postage sticker that some postoffices use to mark the fact that the postage
				has been paid. However, almost universally people are more happy when they receive cards with
				stamps that have interesting images, especially of those match the theme of the card.
			</p>
			EOQ
			,
			// 5
			<<<'EOQ'
			<h3>Where to go to send postcards?</h3>
			<p>
				This depends on what country you are in, or even where you are in that country. Almost all the places
				allow one to send postcards at a local postoffice, but some have post boxes set up at the different locations.
				In some places it is possible to even have the mail carrier that brings you mail to take the cards that you have
				signed.
			</p>
			EOQ
			,
			// 6
			<<<'EOQ'
			<h3>What do I write on a postcards I am sending?</h3>
			<p>
				You absolutely <strong>must</strong> write a code, so that the receiver can register the card. The code
				is the secret given to you and will be in the form of LOC-LOC-2023-101, where LOC is the location of first sender and
				then the receiver of the postcard. Unless you are sending a card in an envelope, you must write an address.
				Everything else is up to you. It is perfectly OK to just say «Here is a Happy Postcard for you.» but many
				people like to share a little bit about themselves, about the card… Some people write the the weather now,
				what music they are listening to while signing, share plans for the future, maybe even share an interesting
				anecdote about one&apos;s life. It is also a custom by many people to decorate their card with stickers, stamps,
				drawings or doodles.
			</p>
			<p>
				Be creative!
			</p>
			EOQ
			,
			// 7
			<<<'EOQ'
			<h3>What if I do not speak the language of the user I am sending a card to?</h3>
			<p>
				You must write an address and the code, other things are a nice addition. If you know at least a couple
				phrases, it is nice to write them. However, it is perfectly OK to write in the language that a you know,
				even when the receiver is not a speaker of that language. Perhaps they will use the opportunity to learn a word
				or two. Or maybe they will just smile and say «I have received a card, it&apos;s a wonderful day, I will assume
				that I am told something very nice».
			</p>
			EOQ
			,
			// 8
			<<<'EOQ'
			<h3>What is a «postcard»?</h3>
			<p>
				Believe it or not, this is a cause of contravercy in this hobby. If you send rectangular, non-folding
				cardboard that have an image on one side and address, postage, and a place for your writing on the other,
				you will always make people happy. But many users will be glad to receive folded greeting cards, official
				postal cards (with address and postage on one side and text on the other), shaped cards, and hand-made
				cards. A few users actually request their cards sent in an envelope.
			</p>
			<p>
				Still unsure? Send what you would like to receive yourself and be happy to call it a card.
			</p>
			EOQ
			,
			// 9
			<<<'EOQ'
			<h3>Is postcard exchange the same thing as philacardy or deltiology?</h3>
			<p>
				Philacardy and deltiology is the act of collecting and studying postcards. Many people who engage in
				postcard exchange also collect some types of postcards. However, it must be noted that in postcard exchange
				it is the sender who has the final say on what postcard is being sent. Demanding specific cards is rude, and
				refusing to register a card that has been received is a potential ground for termination of an account.
			</p>
			EOQ
			,
			// 10
			<<<'EOQ'
			<h3>What if there is a subject that triggers me emotionally?</h3>
			<p>
				Happy Postcard provides field in your profile to let people know of phobias that you may have. Put them there
				if you cannot deal with some subjects, so that people know to tryto avoid such themes. Keep in mind, that it may
				still be possible that a person simply doesn't have another postcard to send you.
			</p>
			EOQ
			,
			// 11
			<<<'EOQ'
			<h3>How often may I send a postcard?</h3>
			<p>
				For now there are no arbitrary restrictions. When the community will grow, we may start limiting the number of
				travelling postcards that somebody may have. For now the limit is that the site needs to have an available address
				to give you.
			</p>
			<p>You randomly get an address when the following are <strong>not</strong> true:</p>
			<ol>
				<li>You have a travelling postcard to that user that is sent within 2 months</li>
				<li>You have a sent card to that user that has arrived and was sent within 2 weeks</li>
				<li>User is waiting to receive 5 postcards, but has sent nothing</li>
				<li>User is waiting to receive 15 postcards, but none of their sent cards have arrived</li>
				<li>The user sent out less than half of what is sent to them</li>
			</ol>
			EOQ
			,
			// 12
			<<<'EOQ'
			<h3>Where to write the code of the card?</h3>
			<p>
				Write it on the card itself. Write it clearly and legibly somewhere not too close to the address and stamps.
				If you write too close to the address it is possible that the mail couriers will get confused and interpret
				it as a part of it. If you write too close to the stamps it's possible that the cancellation will make your ID
				impossible to read.
			</p>
			<p>
				Some people prefer to write the code more than once, to make it easier to be found. This is a good idea, but is
				not needed most of the time.
			</p>
			EOQ
			,
			// 13
			<<<'EOQ'
			<h3>What to do if I cannot figure out the code of the card that I have received?</h3>
			<p>
				Short answer is: <a href='/help'>contact</a> and provide as much information as you can. If you can make out
				even a small part of the code, this may help out a lot to locate the correct one. Date that the postcard is sent
				is also something that provides a lot of information, which will aid in findin the correct code.
			</p>
			EOQ
			,
			// 14
			<<<'EOQ'
			<h3>How late may I register the card?</h3>
			<p>
				You should strive to register the card as soon as it is possible after it arrives, but sometimes you cannot
				do it right away, so just register it at your earlies convenience. Happy Postcard does not have any period after
				which the card can no longer be registered. Even if the card has arrived after travelling for 3 years, you have
				received it, and that is all that matters!
			</p>
			EOQ
			,
			// 15
			<<<'EOQ'
			<h3>How early may I register the card?</h3>
			<p>
				It is a very bad idea to register the card before you are physically holding it. There are a few exceptions to
				this, a sender writing you and asking to register because they think that the card has gotten lost is
				<strong>not</strong> one such exception.
			</p>
			<p>
				The site gives out the address of the person more when their cards have arrived, by registering before the card
				is received you make it unfair for other people on this site. You also can create a culture where people do not
				send cards at all, and just claim that they do.
			</p>
			EOQ
			,
			// 16
			<<<'EOQ'
			<h3>What if the card gets lost?</h3>
			<p>
				Unfortunately this is the fact of this hobby. In other words: It is not «if they get lost», but «when they get lost».
				When you receive the address, you are expected to send a card. But then that is it. You may, however, try to send
				a second card with the same number in order to hope that it will actually arrive.
			</p>
			<p>
				What you should <strong>not</strong> do is request that another user registers your card, even though they did
				not get it. This is rude and uncalled for behaviour.
			</p>
			EOQ
			,
			// 17
			<<<'EOQ'
			<h3>Should I tell a person, that I have sent a card to them?</h3>
			<p>
				Don&apos;t. Let it be a surprise. Happy Postcard already tells the person that they are going to receive a card from
				somebody, that is more than enough information for the unspoiled expectation.
			</p>
			EOQ
			,
			// 18
			<<<'EOQ'
			<h3>What images should be uploaded on a postcard page?</h3>
			<p>
				It is always nice to upload a true and correct depiction of a postcard. If you have a scanner, scan
				the image portion of a card, if not you can always take a photograph. Keep in mind, however, that you
				must <strong>not</strong> upload the portion depicting the addresses that is considered to be
				a violation of privacy, and such images will be deleted by an administrator, when found.
			</p>
			<p>
				Some people also upload images of stamps used to send the postcard, that is wondeful, but try
				to upload the image of the card as the first image in that scenario.
			</p>
			EOQ
			,
			// 19
			<<<'EOQ'
			<h3>How and when did Happy Postcard start?</h3>
			<p>
				The <a href='/user/v010dya'>developer of this site</a> is a very engaged postcrosser, who sends out many
				postcards and is on many different sites. Unfortunately sometimes there are no available receivers and also
				there are features that are not present on other websites. For example, there was only one site (that is now
				closed) that allowed a user to represent not a country, but a region within a country.
			</p>
			<p>
				The development began in 2021, with hopes to make the site public that same year. Unfortunately it took more time
				to complete, and the alpha testing started on 2023-10-01, and beta testing open to all on 2023-11-12.
			</p>
			EOQ
			,
			// 20
			<<<'EOQ'
			<h3>Does Happy Postcard compete against other similar websites?</h3>
			<p>
				Absolutely not! The goals are quite similar to many other websites, but there is (and should not be)
				no animosity against any other developers or communities. All users are welcome, and it is quite
				common to see users on multiple platforms. It is nice to invite people from other platforms to check out
				this one, but the goal is not for any other website to shut down or for any users to stop participating
				in other communities.
			</p>
			EOQ
			,
			// 21
			<<<'EOQ'
			<h3>How to format the profile?</h3>
			<p>
				Currently very limited markup is available. If you make an empty line, that starts a paragraph.
				If without an empty line carriage returns are preseved, this allows you to make lists.
				URLs are <strong>not</strong> parsed and will remain as text. No other markup is available.
			</p>
			<p>
				For security reasons some characters are replaced in the profile text, the intention is to have
				characters that look similar enough. For example the character &lt; will be replaced with 〈.
			</p>
EOQ
			, // indenting the las close incorrectly just to pacify the IDE
		];
	}
	public function setEditor(User $editor) : void
	{}
	public function setTemplateParameter(array $parameter) : void
	{
		if(!isset($parameter['questions']))
		{
			$this->displayedQuestions = range(0, count($this->questions)-1);
		}
		else if(is_array($parameter['questions']))
		{
			foreach($parameter['questions'] as $qNum)
			{
				$this->displayedQuestions[] = intval($qNum);
			}
		}
		
		if(isset($parameter['type']) and $parameter['type'] == 'random')
		{
			$this->displayedQuestions = [ $this->displayedQuestions[array_rand($this->displayedQuestions)] ];
		}
	}
	public function setPerformerResults($performerResults) : void
	{}
	public function invoke() : void
	{
		foreach($this->displayedQuestions as $qNum)
		{
			echo "<!-- faq {$qNum} -->";
			echo $this->questions[$qNum];
		}
		
		$this->displayed = true;
	}
	public function haveDisplayed() : bool
	{
		return $this->displayed;
	}
}