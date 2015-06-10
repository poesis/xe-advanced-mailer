
Advanced Mailer Module for XE
=============================

서버의 `mail()` 함수에 의존하지 않고도 다양한 외부 서비스를 이용하여
메일을 발송할 수 있도록 해주는 모듈입니다.

현재 아래와 같은 서비스를 지원합니다.

  - SMTP (Gmail, Outlook.com, Yahoo, 한메일, 네이버 등)
  - [Amazon SES](https://aws.amazon.com/ses/)
  - [Mailgun](http://www.mailgun.com/)
  - [Mandrill](https://www.mandrill.com/)
  - [Postmark](https://postmarkapp.com/)
  - [SendGrid](https://sendgrid.com/)
  - [우리메일](http://woorimail.com/)

외부 SMTP 서버나 메일 발송 전문업체의 API를 사용하면
일반적인 웹호스팅 서버에서 직접 메일을 발송하는 것보다 훨씬 안정적으로 메일이 전달되고,
업체에서 안내하는 대로 도메인에 SPF, DKIM 등을 적용하면
자신의 도메인을 그대로 사용하면서도 스팸 필터에 걸릴 확률이 크게 줄어듭니다.
가입인증을 비롯한 중요한 메일이 잘 도착하지 않아 어려움을 겪으셨다면
이 모듈을 사용해 보세요.

XE에 내장된 `Mail` 클래스를 대체하므로
회원가입 인증메일 발송, ID/비밀번호 찾기 등
`Mail` 클래스에 의존하는 기존의 모든 코드를 그대로 사용할 수 있습니다.

기본적인 HTML 메일 발송 기능은 모든 API에서 동일하게 사용할 수 있으나,
파일 첨부 가능 여부, 최대 첨부 용량, CID 첨부 가능 여부, 커스텀 헤더 등은
API에 따라 차이가 있으니 반드시 충분한 테스트를 거치시기 바랍니다.

클래스 오토로딩이 적용된 **XE 1.8.3 이상, PHP 5.4 이상**에서 사용 가능하며,
PHP 5.3에서도 설치할 수는 있으나 일부 발송 방식을 사용할 수 없습니다.

**[주의]** 인증 및 알림 용도로 소량의 메일을 자주 발송하기에 적합한 모듈입니다.
전체공지나 광고 등 대량메일을 발송할 때는 전문업체를 직접 이용하시기 바랍니다.

**[주의]** 이 모듈의 개발자는 이 모듈이 지원하는 API 제공 업체들과 아무 관계도 없으며,
공개된 무료 및 유료 API와 연동하는 프로그램을 제작하여 배포할 뿐입니다.
API 제공 업체의 서비스 품질이나 정책 변경에 대하여
이 모듈의 개발자는 어떠한 책임도 지지 않습니다.
사용자는 각 API의 이용 방법 및 조건들을 숙지하여야 하며,
자신의 사이트를 통해 스팸이 발송되지 않도록 주의를 기울여야 합니다.


설치 방법
---------

설치할 경로는 `./modules/advanced_mailer`입니다.

반드시 관리 모듈에서 모듈 설치를 클릭하여 트리거가 등록되도록 해주어야 합니다.

XE 1.8.3 미만 버전에서 이 모듈을 사용하려면 XE에 내장된 `Mail` 클래스를 사용하지 않도록 조치해 주어야 합니다.
(내장 `Mail` 클래스가 먼저 로딩될 경우 모듈이 동작하지 않습니다.)
내장 `Mail` 클래스를 사용하지 않도록 하는 방법은 두 가지가 있습니다.

1) `config/config.inc.php`에서 `Mail` 클래스를 로딩하는 부분을 주석처리(`//`)해 줍니다.

2) `classes/mail/Mail.class.php`에서 `Mail` 클래스의 이름을 다른 것으로 바꾸어 줍니다.
(예: `Mail_Original`)

XE 1.8.3 미만 버전에서는 코어를 업데이트할 경우 수정내역이 원상복구될 수 있으니 주의하시기 바랍니다.


설정하기
--------

### PHP mail() 함수

서버 자체의 메일 발송 기능을 사용합니다. XE에 내장된 `Mail` 클래스와 큰 차이가 없습니다.

### 외부 SMTP 서버

Gmail, 한메일, 네이버 메일 등 주요 포털의 서버 정보를 쉽게 선택할 수 있으며,
그 밖에도 SMTP를 지원하는 메일 계정이라면 모두 사용할 수 있습니다.

Gmail의 경우 구글 계정의 보안 설정 페이지에서 "보안 수준이 낮은 앱 허용"을 켜지 않으면
SMTP 로그인이 되지 않으니 주의하시기 바랍니다.
네이버, 다음 등 국내 포털은 반드시 환경설정에서 POP3/SMTP 접속을 허용하도록 설정해야 합니다.
그 밖에도 각 포털의 일일 발송량 제한 정책을 잘 파악하시기 바랍니다.

  - SMTP 서버: 서버명
  - SMTP 포트: 465, 587 또는 25
  - SMTP 보안: 465번 포트인 경우 `SSL`, 587번 포트인 경우 `TLS`, 25번 포트인 경우 사용하지 않음
  - 아이디: 메일 계정 아이디
  - 비밀번호: 메일 계정 비밀번호

### Amazon SES API

클라우드 서비스로 유명한 아마존에서 운영하는 메일 발송 서비스입니다.
메일 1건당 가격이 매우 저렴하다는 장점이 있으나, 처음 셋팅하는 과정이 다소 복잡하고
일부 포털에는 배달이 되지 않거나 스팸으로 취급받기도 하니 주의하시기 바랍니다.

  - AWS Region: AWS Region (예: `us-east-1`)
  - AWS Access Key: AWS Access Key
  - AWS Secret Key: AWS Secret Key

### Mailgun API

대형 IDC 업체인 Rackspace에서 운영하는 API입니다.
셋팅은 비교적 간단하고, 월 10,000건까지 무료로 발송할 수 있습니다.
설정할 때 반드시 도메인을 입력해야 하니 주의하세요.

  - 도메인: Mailgun에 등록된 도메인 입력
  - API Key: Mailgun에서 발급받은 API key 입력

### Mandrill API

대량메일 발송 전문업체인 Mailchimp에서 트랜잭션 메일 발송을 위해 별도로 개발한 상품입니다.
셋팅이 매우 간단하고, 월 12,000건까지 무료로 발송할 수 있습니다.

  - API Key: Mandrill에서 발급받은 API key 입력

### Postmark API

트랜잭션 메일 발송 전문업체로, 대량메일은 취급하지 않습니다.
셋팅이 매우 간단하고, 최초 가입시 25,000건을 발송할 수 있는 포인트가 적립됩니다.

  - API Key: Postmark에서 발급받은 API key 입력

### SendGrid API

대량메일 발송 전문업체이며, 월 회비가 있지만 하루 400건(월 12,000건)까지는
무료로 사용할 수 있습니다. 셋팅은 비교적 간단합니다.

  - 아이디: SendGrid 회원 아이디
  - 비밀번호: SendGrid 회원 비밀번호

### 우리메일 API

XE 기반으로 운영되는 국내 대량메일 발송 전문업체입니다.
월 10,000건까지 무료로 사용할 수 있습니다.
보낸이의 메일주소를 직접 지정하는 기능은 유료 서비스입니다.
파일첨부는 지원하지 않으며 메일 내 외부링크로 해결해야 합니다.

  - 도메인: 인증키 발급시 입력한 도메인
  - API Key: 우리메일에서 발급받은 인증키


트리거 제공 및 디버깅 안내
--------------------------

메일 전송 전후에 호출되는 `advanced_mailer.send` (`before`, `after`) 트리거를 이용하여
내용을 변경하고 BCC를 추가하거나 발송 내역을 DB에 기록하는 등의 작업을 할 수 있습니다.
현재 작성중인 메일 오브젝트를 넘겨드립니다.

메일 발송에 실패한 경우 `send()` 메소드가 `false`를 반환합니다.
이 때 메일 오브젝트의 `errors` 속성을 분석하면 어떤 오류가 발생했는지 알 수 있습니다.
(배열 형태로 제공됩니다.)


라이선스
--------

이 모듈은 XE와 동일한 LGPL v2.1 라이선스의 적용을 받습니다.
