unit Unit1;

interface

uses
  Windows, Messages, SysUtils, Variants, Classes, Graphics, Controls, Forms,
  Dialogs, StdCtrls, ExtCtrls, OleCtrls, SHDocVw, SHDocVw_EWB, EwbCore,
  EmbeddedWB;

type
  TForm1 = class(TForm)
    pnl1: TPanel;
    btn1: TButton;
    btn2: TButton;
    open: TOpenDialog;
    btn3: TButton;
    wb: TEmbeddedWB;
    procedure FormCreate(Sender: TObject);
    procedure btn1Click(Sender: TObject);
    procedure btn3Click(Sender: TObject);
    procedure btn2Click(Sender: TObject);

  private
    { Private declarations }
  public
    { Public declarations }
  end;

var
  Form1: TForm1;

implementation

{$R *.dfm}


procedure TForm1.FormCreate(Sender: TObject);
begin
  WinExec('d:\mypost\pnlite\bin\httpd.exe -k install -n Pn_Apache',SW_HIDE);
  WinExec('net start pn_apache',SW_HIDE);
  wb.Navigate('http://127.0.0.1:8181/run.php?r=config');
end;

procedure TForm1.btn1Click(Sender: TObject);
begin
  WinExec('net start pn_apache',SW_HIDE);
  wb.Navigate('http://127.0.0.1:8181/run.php?r=config');
end;

procedure TForm1.btn3Click(Sender: TObject);
begin
  if open.Execute then
  begin
    CopyFile(pchar(open.FileName),'D:\mypost\PnLite\htdocs\cs.xls',false);
    ShowMessage(PChar(open.FileName)+'文件选择成功！');
  end;

end;

procedure TForm1.btn2Click(Sender: TObject);
begin
  //WinExec('D:\mypost\PnLite\bin\php.exe D:\mypost\PnLite\htdocs\run.php',SW_SHOW);
    WinExec('D:\mypost\run.bat',SW_SHOW);
end;

end.
